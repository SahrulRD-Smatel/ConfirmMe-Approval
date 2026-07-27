<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\EmployeeLoan;
use App\Models\Document;
use App\Models\LoanRepayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear permission cache to avoid "table not found" or caching issues in tests
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        // Create necessary roles
        Role::firstOrCreate(['name' => 'staff']);
        Role::firstOrCreate(['name' => 'finance']);
    }

    protected function createDocument(User $user)
    {
        return Document::create([
            'document_number' => 'DOC-' . uniqid(),
            'title' => 'Test Loan',
            'approval_type' => 'loan',
            'user_id' => $user->id,
            'department' => $user->department,
            'status' => 'approved', // Assuming document is approved before loan activates
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_loan_disbursement_sets_correct_status_and_balance()
    {
        // Arrange
        $finance = User::factory()->create([
            'department' => 'Finance',
            'position' => 'Staff',
            'email' => 'finance@example.com' // Avoid random dupes
        ]);
        $finance->assignRole('finance');

        $staff = User::factory()->create([
            'department' => 'IT',
            'position' => 'Staff',
            'email' => 'staff@example.com'
        ]);
        $staff->assignRole('staff');

        $document = $this->createDocument($staff);

        $loan = EmployeeLoan::create([
            'document_id' => $document->id,
            'employee_id' => $staff->id,
            'created_by' => $staff->id,
            'loan_amount' => 5000000,
            'installment_months' => 5,
            'monthly_installment' => 1000000,
            'outstanding_amount' => 5000000,
            'status' => 'pending_disbursement',
            'loan_number' => 'LOAN-' . uniqid(),
        ]);

        // Act: Disburse
        $response = $this->actingAs($finance)
            ->post(route('finance.loans.disburse', $loan));

        // Assert
        // Depending on controller, it might redirect or return success
        // We check the DB state
        $loan->refresh();
        $this->assertEquals('disbursed', $loan->status);
        $this->assertEquals(5000000, $loan->outstanding_amount);
        $this->assertNotNull($loan->disbursement_date);
    }

    public function test_repayment_deducts_outstanding_balance_correctly()
    {
        // Arrange
        $finance = User::factory()->create(['department' => 'Finance', 'position' => 'Manager']);
        $finance->assignRole('finance');

        $staff = User::factory()->create(['department' => 'IT', 'position' => 'Staff']);
        $staff->assignRole('staff');

        $document = $this->createDocument($staff);

        $loan = EmployeeLoan::create([
            'document_id' => $document->id,
            'employee_id' => $staff->id,
            'created_by' => $staff->id,
            'loan_amount' => 5000000,
            'installment_months' => 5,
            'monthly_installment' => 1000000,
            'outstanding_amount' => 5000000, // Full amount
            'status' => 'disbursed', // Already disbursed
            'disbursed_at' => now()->subMonth(),
            'loan_number' => 'LOAN-' . uniqid(),
        ]);

        // Act: Make Repayment of 1,000,000
        $response = $this->actingAs($finance)
            ->post(route('finance.loans.repayment', $loan), [
                'amount' => 1000000,
                'payment_method' => 'payroll_deduction',
                'notes' => 'Installment 1',
                'payment_date' => now()->toDateString(),
            ]);

        // Assert
        $loan->refresh();
        $this->assertEquals(4000000, $loan->outstanding_amount);
        $this->assertEquals(1000000, $loan->total_paid);
        $this->assertEquals('repaying', $loan->status);

        $this->assertDatabaseHas('loan_repayments', [
            'employee_loan_id' => $loan->id,
            'amount' => 1000000,
        ]);
    }

    public function test_loan_auto_completes_when_balance_is_zero()
    {
        // Arrange
        $finance = User::factory()->create(['department' => 'Finance', 'position' => 'Manager']);
        $finance->assignRole('finance');

        $staff = User::factory()->create(['department' => 'IT', 'position' => 'Staff']);
        $staff->assignRole('staff');

        $document = $this->createDocument($staff);

        $loan = EmployeeLoan::create([
            'document_id' => $document->id,
            'employee_id' => $staff->id,
            'created_by' => $staff->id,
            'loan_amount' => 1000000,
            'installment_months' => 1,
            'monthly_installment' => 1000000,
            'outstanding_amount' => 1000000,
            'status' => 'disbursed',
            'loan_number' => 'LOAN-' . uniqid(),
        ]);

        // Act: Repay Full Amount
        $this->actingAs($finance)
            ->post(route('finance.loans.repayment', $loan), [
                'amount' => 1000000,
                'payment_method' => 'cash',
                'payment_date' => now()->toDateString(),
            ]);

        // Assert
        $loan->refresh();
        $this->assertEquals(0, $loan->outstanding_amount);
        $this->assertEquals('paid_off', $loan->status); // Status defined in model is 'paid_off'
    }
}
