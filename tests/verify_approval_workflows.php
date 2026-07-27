<?php

use App\Models\User;
use App\Models\Document;
use App\Services\ApprovalWorkflowService;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(ApprovalWorkflowService::class);

// Helpers
function getUser($role, $dept = null)
{
    $query = User::role($role);
    if ($dept) {
        $query->where('department', $dept);
    }
    return $query->first() ?? User::role($role)->first();
}

function testWorkflow($service, $type, $amount = 0)
{
    echo "\n--- Testing Workflow: " . strtoupper($type) . " ---\n";

    DB::beginTransaction();

    try {
        // 1. Setup Actors
        $staff = getUser('staff', 'IT');
        if (!$staff)
            die("Error: Staff user not found.\n");

        echo "Requester: " . $staff->name . " (" . $staff->email . ")\n";

        // 2. Create Document
        $doc = Document::create([
            'user_id' => $staff->id,
            'approval_type' => $type,
            'document_number' => 'TEST/' . strtoupper($type) . '/' . rand(1000, 9999),
            'status' => 'draft', // Initial status
            'title' => "Test $type Workflow",
            'amount' => $amount,
            'description' => "Automated test for $type workflow.",
            'department' => 'IT',
            'date' => now(),
        ]);

        echo "Document Created: " . $doc->document_number . " (Status: " . $doc->status . ")\n";

        // 3. Submit Document
        $service->initializeWorkflow($doc);
        $doc->refresh();
        echo "Document Submitted -> Status: " . $doc->status . "\n";

        // 4. Loop Approvals
        while (!in_array($doc->status, ['approved', 'rejected'])) {
            $approverId = $doc->current_approver_id;

            if (!$approverId) {
                echo "Error: No current approver defined but status is " . $doc->status . "\n";
                break;
            }

            $approver = User::find($approverId);
            echo "Current Approver: " . $approver->name . " (" . $approver->getRoleNames()->first() . ")\n";

            // Approve
            $service->processApproval($doc, $approver, 'approved', "Approved by script.");
            $doc->refresh();

            echo "Action: Approved -> New Status: " . $doc->status . "\n";
        }

        echo "Final Status: " . $doc->status . "\n";

        if ($doc->status === 'approved') {
            echo "✅ Workflow $type PASSED\n";
        } else {
            echo "❌ Workflow $type FAILED\n";
        }

        DB::rollBack(); // Don't persist test data
        echo "Transaction Rolled Back (Clean State)\n";

    } catch (\Exception $e) {
        DB::rollBack();
        echo "❌ Exception: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString() . "\n";
    }
}

// Run Tests
testWorkflow($service, 'only', 0);
testWorkflow($service, 'po', 50000000);
testWorkflow($service, 'invoice', 50000000);
