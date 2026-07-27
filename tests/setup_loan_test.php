<?php

use App\Models\User;
use App\Models\Document;
use App\Models\EmployeeLoan;
use Illuminate\Support\Str;

// 1. Get Users
$staff = User::where('email', 'staff.it@confirmme.com')->first();
$finance = User::where('email', 'manager.finance@confirmme.com')->first();

if (!$staff || !$finance) {
    echo "Error: Required users not found.\n";
    exit(1);
}

// 2. Create Dummy Document
$document = Document::create([
    'user_id' => $staff->id,
    'approval_type' => 'LOAN',
    'document_number' => 'DOC/TEST/' . rand(1000, 9999),
    'status' => 'approved',
    'title' => 'Test Loan for Repayment',
    'amount' => 12000000,
    'description' => 'Test loan generated for repayment verification.',
    'department' => $staff->department ?? 'General',
    'date' => now(),
]);

echo "Created Document: " . $document->document_number . "\n";

// 3. Create Disbursed Loan
$loan = EmployeeLoan::create([
    'document_id' => $document->id,
    'employee_id' => $staff->id,
    'loan_amount' => 12000000,
    'installment_months' => 12,
    'monthly_installment' => 1000000,
    'outstanding_amount' => 12000000,
    'total_paid' => 0,
    'status' => 'disbursed',
    'disbursement_date' => now()->subDays(15),
    'disbursed_by' => $finance->id,
    'created_by' => $finance->id,
    'notes' => 'Seeded for testing.',
]);

echo "Created Loan: " . $loan->loan_number . "\n";
echo "Employee: " . $staff->name . "\n";
echo "Status: " . $loan->status . "\n";
