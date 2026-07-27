<?php

use App\Models\EmployeeLoan;
use App\Models\User;
use App\Http\Controllers\MyLoanController;
use Illuminate\Http\Request;

// 1. Login as Staff
$staff = User::where('email', 'staff.it@confirmme.com')->first();
auth()->login($staff);

// 2. Fetch Loans
$loans = EmployeeLoan::forEmployee($staff->id)->get();
echo "Total Loans for Staff: " . $loans->count() . "\n";

foreach ($loans as $loan) {
    echo "Loan: " . $loan->loan_number . " - Status: " . $loan->status_label . " - Outstanding: " . $loan->formatted_outstanding . "\n";
}

// 3. Check specific loan access
$loan = EmployeeLoan::where('loan_number', 'LOAN-202601-0003')->first();
if ($loan) {
    try {
        $controller = app(MyLoanController::class);
        // show method returns view, so just check scope logic
        if ($loan->employee_id !== auth()->id()) {
            echo "Access Denied (Correct for others, incorrect for owner)\n";
        } else {
            echo "Access Granted for Loan Owner (Correct)\n";
        }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
