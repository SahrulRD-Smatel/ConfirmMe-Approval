<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LeaveRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class LeaveRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear permission cache to avoid "table not found" or caching issues in tests
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Create necessary roles
        Role::create(['name' => 'staff']);
        Role::create(['name' => 'supervisor']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'hrd']);
        Role::create(['name' => 'direktur_hr']);
        Role::create(['name' => 'direktur_finance']);
        Role::create(['name' => 'direktur_it']);
        Role::create(['name' => 'direktur_sales']);
        Role::create(['name' => 'presiden_direktur']);
    }

    /**
     * Test full 2-tier flow: annual leave deducts balance after both tiers approve.
     */
    public function test_annual_leave_deducts_balance_when_fully_approved()
    {
        // 1. Arrange
        // HR Manager (acts as Tier 2 approver)
        $hrManager = User::factory()->create(['department' => 'Human Resources', 'annual_leave_balance' => 12]);
        $hrManager->assignRole(['manager', 'hrd']);

        // Supervisor (acts as Tier 1 approver)
        $supervisor = User::factory()->create(['department' => 'IT', 'annual_leave_balance' => 12]);
        $supervisor->assignRole('supervisor');

        // Staff (requester)
        $staff = User::factory()->create(['department' => 'IT', 'annual_leave_balance' => 12]);
        $staff->assignRole('staff');

        // Create Leave Request
        $leaveRequest = LeaveRequest::create([
            'user_id' => $staff->id,
            'type' => 'annual',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2), // 2 days
            'reason' => 'Holiday',
            'status' => 'pending'
        ]);

        // 2. Act - Tier 1: Supervisor approves
        $this->actingAs($supervisor)
            ->post(route('leave-approvals.approve', $leaveRequest));

        $leaveRequest->refresh();
        $this->assertEquals('pending_hr', $leaveRequest->status, 'After Tier 1, status should be pending_hr');

        // Balance NOT yet deducted at Tier 1
        $staff->refresh();
        $this->assertEquals(12, $staff->annual_leave_balance, 'Balance should NOT decrease after Tier 1');

        // 3. Act - Tier 2: HR Manager approves
        $response = $this->actingAs($hrManager)
            ->post(route('leave-approvals.approve', $leaveRequest));

        // Debug: check response status
        $response->assertRedirect(); // should redirect, not 403

        // 4. Assert
        $leaveRequest->refresh();
        $this->assertEquals('approved', $leaveRequest->status, 'After Tier 2, status should be approved');

        $staff->refresh();
        $this->assertEquals(10, $staff->annual_leave_balance, 'Balance should decrease by 2 days after final approval');
    }

    /**
     * Test sick leave increments counter (not deducts balance) after full approval.
     */
    public function test_sick_leave_increments_counter_when_fully_approved()
    {
        // 1. Arrange
        $hrManager = User::factory()->create(['department' => 'Human Resources']);
        $hrManager->assignRole(['manager', 'hrd']);

        $supervisor = User::factory()->create(['department' => 'IT']);
        $supervisor->assignRole('supervisor');

        $staff = User::factory()->create(['department' => 'IT', 'annual_leave_balance' => 12, 'sick_leave_used' => 0]);
        $staff->assignRole('staff');

        $leaveRequest = LeaveRequest::create([
            'user_id' => $staff->id,
            'type' => 'sick',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDay(), // 1 day
            'reason' => 'Sick',
            'status' => 'pending'
        ]);

        // 2. Act - Tier 1
        $this->actingAs($supervisor)
            ->post(route('leave-approvals.approve', $leaveRequest));

        // Act - Tier 2
        $this->actingAs($hrManager)
            ->post(route('leave-approvals.approve', $leaveRequest->fresh()));

        // 3. Assert
        $staff->refresh();
        $this->assertEquals(12, $staff->annual_leave_balance, 'Sick leave should not deduct annual balance');
        $this->assertEquals(1, $staff->sick_leave_used, 'Sick leave counter should increment');
    }

    /**
     * Test that staff cannot access approval routes.
     */
    public function test_non_hr_staff_cannot_approve_leave_requests()
    {
        $staff1 = User::factory()->create(['department' => 'IT']);
        $staff1->assignRole('staff');

        $staff2 = User::factory()->create(['department' => 'IT']);
        $staff2->assignRole('staff');

        $leaveRequest = LeaveRequest::create([
            'user_id' => $staff2->id,
            'type' => 'annual',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDay(),
            'reason' => 'Test',
            'status' => 'pending'
        ]);

        $response = $this->actingAs($staff1)
            ->post(route('leave-approvals.approve', $leaveRequest));

        $response->assertForbidden(); // Non-HR staff cannot approve

        $leaveRequest->refresh();
        $this->assertEquals('pending', $leaveRequest->status);
    }

    /**
     * Test supervisor can only approve leave from staff in their own department.
     */
    public function test_supervisor_can_only_approve_own_department_staff()
    {
        // HR Manager for Tier 2
        $hrManager = User::factory()->create(['department' => 'Human Resources']);
        $hrManager->assignRole(['manager', 'hrd']);

        // IT Supervisor
        $supervisorIT = User::factory()->create(['department' => 'IT']);
        $supervisorIT->assignRole('supervisor');

        // IT Staff
        $staffIT = User::factory()->create(['department' => 'IT']);
        $staffIT->assignRole('staff');

        // Sales Staff
        $staffSales = User::factory()->create(['department' => 'Sales']);
        $staffSales->assignRole('staff');

        // Request from IT Staff
        $requestIT = LeaveRequest::create([
            'user_id' => $staffIT->id,
            'type' => 'annual',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDay(),
            'reason' => 'Test',
            'status' => 'pending'
        ]);

        // Request from Sales Staff
        $requestSales = LeaveRequest::create([
            'user_id' => $staffSales->id,
            'type' => 'annual',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDay(),
            'reason' => 'Test',
            'status' => 'pending'
        ]);

        // Act & Assert 1: Supervisor approves IT staff (Allowed → pending_hr)
        $response1 = $this->actingAs($supervisorIT)
            ->post(route('leave-approvals.approve', $requestIT));

        $response1->assertRedirect();
        $this->assertEquals('pending_hr', $requestIT->fresh()->status, 'IT supervisor should be able to approve IT staff (moves to pending_hr)');

        // Act & Assert 2: Supervisor tries to approve Sales staff (Forbidden)
        $response2 = $this->actingAs($supervisorIT)
            ->post(route('leave-approvals.approve', $requestSales));

        $response2->assertForbidden();
        $this->assertEquals('pending', $requestSales->fresh()->status, 'IT supervisor should NOT approve Sales staff');
    }

    /**
     * Test rejection works properly with reason saved.
     */
    public function test_supervisor_can_reject_own_dept_staff_leave()
    {
        $supervisor = User::factory()->create(['department' => 'IT']);
        $supervisor->assignRole('supervisor');

        $staff = User::factory()->create(['department' => 'IT']);
        $staff->assignRole('staff');

        $leaveRequest = LeaveRequest::create([
            'user_id' => $staff->id,
            'type' => 'annual',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDay(),
            'reason' => 'Test',
            'status' => 'pending'
        ]);

        $response = $this->actingAs($supervisor)
            ->post(route('leave-approvals.reject', $leaveRequest), [
                'rejection_reason' => 'Proyek deadline minggu ini',
            ]);

        $response->assertRedirect();
        $leaveRequest->refresh();
        $this->assertEquals('rejected', $leaveRequest->status);
        $this->assertEquals('Proyek deadline minggu ini', $leaveRequest->rejection_reason);
        $this->assertEquals($supervisor->id, $leaveRequest->approved_by);
    }

    /**
     * Test that cross-department rejection is blocked.
     */
    public function test_supervisor_cannot_reject_other_dept_leave()
    {
        $supervisorIT = User::factory()->create(['department' => 'IT']);
        $supervisorIT->assignRole('supervisor');

        $staffSales = User::factory()->create(['department' => 'Sales']);
        $staffSales->assignRole('staff');

        $leaveRequest = LeaveRequest::create([
            'user_id' => $staffSales->id,
            'type' => 'annual',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDay(),
            'reason' => 'Test',
            'status' => 'pending'
        ]);

        $response = $this->actingAs($supervisorIT)
            ->post(route('leave-approvals.reject', $leaveRequest), [
                'rejection_reason' => 'Testing cross-dept',
            ]);

        $response->assertForbidden();
        $this->assertEquals('pending', $leaveRequest->fresh()->status);
    }

    /**
     * Test Presiden Direktur can approve director leave and skip HR tier.
     */
    public function test_presiden_can_approve_director_leave_directly()
    {
        $presiden = User::factory()->create(['department' => 'Board of Directors']);
        $presiden->assignRole('presiden_direktur');

        $direkturIT = User::factory()->create(['department' => 'IT']);
        $direkturIT->assignRole('direktur_it');

        $leaveRequest = LeaveRequest::create([
            'user_id' => $direkturIT->id,
            'type' => 'annual',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(3), // 3 days
            'reason' => 'Test',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($presiden)
            ->post(route('leave-approvals.approve', $leaveRequest));

        $response->assertRedirect();

        $leaveRequest->refresh();
        // Presiden skips HR validation → directly approved
        $this->assertEquals('approved', $leaveRequest->status, 'Presiden should approve directly without HR tier');
        $this->assertEquals($presiden->id, $leaveRequest->approved_by);

        $direkturIT->refresh();
        $this->assertEquals(9, $direkturIT->annual_leave_balance, 'Leave balance should be deducted by 3 days');
    }
}
