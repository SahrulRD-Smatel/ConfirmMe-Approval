<?php

use App\Models\EmployeeLoan;
use App\Http\Controllers\Finance\LoanController;
use Illuminate\Http\Request;
use App\Models\User;

// 1. Get the loan
$loan = EmployeeLoan::where('loan_number', 'LOAN-202601-0003')->first();
if (!$loan) {
    echo "Loan not found.\n";
    exit;
}

// 2. Mock Request
$finance = User::where('email', 'manager.finance@confirmme.com')->first();
auth()->login($finance);

$request = new Request([
    'amount' => 1000000,
    'payment_date' => now()->toDateString(),
    'payment_method' => 'transfer',
    'notes' => 'Test backend repayment',
]);

echo "Initial Outstanding: " . $loan->outstanding_amount . "\n";

// 3. Call Controller Method
try {
    $controller = app(LoanController::class);
    // We can't easily call the controller method because it returns a RedirectResponse which might need session/route context.
    // Instead we will test the Model logic directly which is what the controller calls.
    // Or we can try calling the logic the controller calls.

    $loan->recordRepayment(
        1000000,
        $finance->id,
        [
            'payment_date' => now()->toDateString(),
            'payment_method' => 'transfer',
            'notes' => 'Test backend repayment'
        ]
    );

    echo "Repayment Recorded.\n";

    $loan->refresh();
    echo "New Outstanding: " . $loan->outstanding_amount . "\n";
    echo "Status: " . $loan->status . "\n";
    echo "Repayment Count: " . $loan->repayments()->count() . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
