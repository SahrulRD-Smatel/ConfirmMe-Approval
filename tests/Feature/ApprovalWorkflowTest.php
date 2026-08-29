<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\DocumentApproval;
use App\Models\Department;
use App\Services\ApprovalWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected ApprovalWorkflowService $workflowService;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear Spatie Permission Cache
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        // Create standard roles
        Role::firstOrCreate(['name' => 'staff']);
        Role::firstOrCreate(['name' => 'supervisor']);
        Role::firstOrCreate(['name' => 'manager']);
        Role::firstOrCreate(['name' => 'finance']);
        Role::firstOrCreate(['name' => 'direktur_it']);
        Role::firstOrCreate(['name' => 'direktur_hr']);
        Role::firstOrCreate(['name' => 'presiden_direktur']);

        $this->workflowService = new ApprovalWorkflowService();
    }

    /**
     * Test dynamic workflow initialization.
     */
    public function test_workflow_initialization_creates_correct_approvals()
    {
        // Create requester (explicit override active)
        $staff = User::factory()->create([
            'department' => 'IT',
            'is_active' => true,
        ]);
        $staff->assignRole('staff');

        // Create approver 1: Supervisor IT
        $supervisor = User::factory()->create([
            'department' => 'IT',
            'is_active' => true,
        ]);
        $supervisor->assignRole('supervisor');

        // Create approver 2: Manager IT
        $manager = User::factory()->create([
            'department' => 'IT',
            'is_active' => true,
        ]);
        $manager->assignRole('manager');

        // Create Template with custom workflow array
        $template = DocumentTemplate::create([
            'name' => 'IT Purchase Request',
            'code' => 'IT_PR',
            'description' => 'Template for IT purchases',
            'approval_type' => 'only',
            'workflow' => ['role:supervisor', 'role:manager'],
            'is_active' => true,
            'created_by' => $staff->id,
        ]);

        // Create Document
        $document = Document::create([
            'title' => 'Request new Keyboard',
            'description' => 'For coding',
            'approval_type' => 'only',
            'estimated_budget' => 500000,
            'user_id' => $staff->id,
            'department' => 'IT',
            'template' => 'IT_PR',
            'status' => 'pending',
        ]);

        $this->workflowService->initializeWorkflow($document);

        $document->refresh();

        // Assert approvals were created in order
        $approvals = $document->approvals;
        $this->assertCount(2, $approvals);

        $this->assertEquals($supervisor->id, $approvals[0]->approver_id);
        $this->assertEquals('role:supervisor', $approvals[0]->approver_role);
        $this->assertEquals(1, $approvals[0]->approval_order);
        $this->assertEquals('pending', $approvals[0]->status);

        $this->assertEquals($manager->id, $approvals[1]->approver_id);
        $this->assertEquals('role:manager', $approvals[1]->approver_role);
        $this->assertEquals(2, $approvals[1]->approval_order);
        $this->assertEquals('pending', $approvals[1]->status);

        // Document's current approver should be the first in line
        $this->assertEquals($supervisor->id, $document->current_approver_id);
        $this->assertEquals('supervisor_review', $document->status);
    }

    /**
     * Test Hybrid Approver - Specific User (user:ID)
     */
    public function test_hybrid_approver_handles_specific_user_role()
    {
        $staff = User::factory()->create([
            'department' => 'IT',
            'is_active' => true,
        ]);
        $staff->assignRole('staff');

        // Target specific user
        $specificApprover = User::factory()->create([
            'department' => 'HR',
            'is_active' => true,
        ]);
        $specificApprover->assignRole('manager');

        $template = DocumentTemplate::create([
            'name' => 'Spec PR',
            'code' => 'SPEC_PR',
            'approval_type' => 'only',
            'workflow' => ['user:' . $specificApprover->id],
            'is_active' => true,
            'created_by' => $staff->id,
        ]);

        $document = Document::create([
            'title' => 'Test Spec User',
            'approval_type' => 'only',
            'user_id' => $staff->id,
            'department' => 'IT',
            'template' => 'SPEC_PR',
            'status' => 'pending',
        ]);

        $this->workflowService->initializeWorkflow($document);

        $document->refresh();
        $this->assertEquals($specificApprover->id, $document->current_approver_id);
    }

    /**
     * Test Hybrid Approver - Division Director meta-role
     */
    public function test_hybrid_approver_resolves_director_meta_role()
    {
        $staff = User::factory()->create([
            'department' => 'IT',
            'is_active' => true,
        ]);
        $staff->assignRole('staff');

        // Create Department record to associate IT with direktur_it
        Department::updateOrCreate(
            ['name' => 'IT'],
            [
                'code' => 'IT',
                'director_role' => 'direktur_it',
                'is_active' => true,
            ]
        );

        // Director user
        $director = User::factory()->create([
            'department' => 'IT',
            'is_active' => true,
        ]);
        $director->assignRole('direktur_it');

        $template = DocumentTemplate::create([
            'name' => 'Director PR',
            'code' => 'DIR_PR',
            'approval_type' => 'only',
            'workflow' => ['role:direktur'], // Meta role
            'is_active' => true,
            'created_by' => $staff->id,
        ]);

        $document = Document::create([
            'title' => 'Test Director Meta',
            'approval_type' => 'only',
            'user_id' => $staff->id,
            'department' => 'IT', // Matches Department name
            'template' => 'DIR_PR',
            'status' => 'pending',
        ]);

        // Clear cached departments before workflow runs to ensure department mapping loads
        \Illuminate\Support\Facades\Cache::forget('departments_all');

        $this->workflowService->initializeWorkflow($document);

        $document->refresh();
        $this->assertEquals($director->id, $document->current_approver_id);
    }

    /**
     * Test Hybrid Approver - Department Prioritization vs Global Fallback
     */
    public function test_hybrid_approver_prioritizes_same_department()
    {
        $staff = User::factory()->create([
            'department' => 'IT',
            'is_active' => true,
        ]);
        $staff->assignRole('staff');

        // Supervisor in IT (same department)
        $supervisorIT = User::factory()->create([
            'department' => 'IT',
            'is_active' => true,
        ]);
        $supervisorIT->assignRole('supervisor');

        // Supervisor in HR (different department)
        $supervisorHR = User::factory()->create([
            'department' => 'HR',
            'is_active' => true,
        ]);
        $supervisorHR->assignRole('supervisor');

        $template = DocumentTemplate::create([
            'name' => 'Dept PR',
            'code' => 'DEPT_PR',
            'approval_type' => 'only',
            'workflow' => ['role:supervisor'],
            'is_active' => true,
            'created_by' => $staff->id,
        ]);

        $document = Document::create([
            'title' => 'Test Same Dept',
            'approval_type' => 'only',
            'user_id' => $staff->id,
            'department' => 'IT',
            'template' => 'DEPT_PR',
            'status' => 'pending',
        ]);

        $this->workflowService->initializeWorkflow($document);

        $document->refresh();
        // Should select supervisorIT, not supervisorHR
        $this->assertEquals($supervisorIT->id, $document->current_approver_id);
    }

    /**
     * Test Hybrid Approver - Global Fallback when same department not found
     */
    public function test_hybrid_approver_falls_back_globally_when_no_dept_approver()
    {
        $staff = User::factory()->create([
            'department' => 'IT',
            'is_active' => true,
        ]);
        $staff->assignRole('staff');

        // No supervisor in IT department. Only one globally in HR.
        $supervisorHR = User::factory()->create([
            'department' => 'HR',
            'is_active' => true,
        ]);
        $supervisorHR->assignRole('supervisor');

        $template = DocumentTemplate::create([
            'name' => 'Global Fallback PR',
            'code' => 'GLOBAL_PR',
            'approval_type' => 'only',
            'workflow' => ['role:supervisor'],
            'is_active' => true,
            'created_by' => $staff->id,
        ]);

        $document = Document::create([
            'title' => 'Test Global Fallback',
            'approval_type' => 'only',
            'user_id' => $staff->id,
            'department' => 'IT',
            'template' => 'GLOBAL_PR',
            'status' => 'pending',
        ]);

        $this->workflowService->initializeWorkflow($document);

        $document->refresh();
        // Should fallback to supervisorHR since there is no supervisor in IT
        $this->assertEquals($supervisorHR->id, $document->current_approver_id);
    }

    /**
     * Test estimated budget adjustment and requester confirmation flow
     */
    public function test_budget_adjustment_triggers_requester_confirmation()
    {
        $staff = User::factory()->create([
            'department' => 'IT',
            'is_active' => true,
        ]);
        $staff->assignRole('staff');

        $finance = User::factory()->create([
            'department' => 'Finance',
            'is_active' => true,
        ]);
        $finance->assignRole('finance');

        $template = DocumentTemplate::create([
            'name' => 'Finance PR',
            'code' => 'FIN_PR',
            'approval_type' => 'po',
            'workflow' => ['role:finance'],
            'is_active' => true,
            'created_by' => $staff->id,
        ]);

        $document = Document::create([
            'title' => 'Keyboard PR',
            'approval_type' => 'po',
            'estimated_budget' => 1000000,
            'user_id' => $staff->id,
            'department' => 'IT',
            'template' => 'FIN_PR',
            'status' => 'pending',
        ]);

        $this->workflowService->initializeWorkflow($document);
        $document->refresh();

        // 1. Act: Finance adjusts amount from 1M to 800k
        $this->workflowService->processApproval($document, $finance, 'approved', 'Overbudgeted', 800000);

        $document->refresh();

        // Assert document status changes to pending_requester_confirmation
        $this->assertEquals('pending_requester_confirmation', $document->status);
        $this->assertEquals(800000, $document->confirmed_amount);
        $this->assertEquals(1000000, $document->original_budget);
        $this->assertNull($document->current_approver_id);

        // 2. Act: Requester accepts the new amount
        $this->workflowService->processRequesterConfirmation($document, 'accept');

        $document->refresh();

        // Budget should be updated and document fully approved/completed
        $this->assertEquals(800000, $document->estimated_budget);
        $this->assertEquals('approved', $document->status);
    }

    /**
     * Test revision request and re-submission flow
     */
    public function test_revision_and_resubmit_workflow()
    {
        $staff = User::factory()->create([
            'department' => 'IT',
            'is_active' => true,
        ]);
        $staff->assignRole('staff');

        $supervisor = User::factory()->create([
            'department' => 'IT',
            'is_active' => true,
        ]);
        $supervisor->assignRole('supervisor');

        $template = DocumentTemplate::create([
            'name' => 'Revision PR',
            'code' => 'REV_PR',
            'approval_type' => 'only',
            'workflow' => ['role:supervisor'],
            'is_active' => true,
            'created_by' => $staff->id,
        ]);

        $document = Document::create([
            'title' => 'Service laptop',
            'approval_type' => 'only',
            'user_id' => $staff->id,
            'department' => 'IT',
            'template' => 'REV_PR',
            'status' => 'pending',
        ]);

        $this->workflowService->initializeWorkflow($document);
        $document->refresh();

        // 1. Act: Supervisor requests revision
        $this->workflowService->processApproval($document, $supervisor, 'revision', 'Harap detailkan biaya service');

        $document->refresh();

        // Assert document status changes to revision
        $this->assertEquals('revision', $document->status);
        $this->assertEquals('Harap detailkan biaya service', $document->revision_notes);
        $this->assertEquals(1, $document->revision_count);
        $this->assertEquals($supervisor->id, $document->revision_requested_by);
        $this->assertNull($document->current_approver_id);

        // 2. Act: Requester resubmits
        $this->workflowService->resubmitAfterRevision($document);

        $document->refresh();

        // Assert workflow re-initialized, notes cleared, supervisor becomes current approver again
        $this->assertEquals('supervisor_review', $document->status);
        $this->assertNull($document->revision_notes);
        $this->assertNull($document->revision_requested_by);
        $this->assertEquals($supervisor->id, $document->current_approver_id);
    }
}
