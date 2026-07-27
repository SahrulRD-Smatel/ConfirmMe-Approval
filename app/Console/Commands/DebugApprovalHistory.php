<?php

namespace App\Console\Commands;

use App\Models\DocumentApproval;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DebugApprovalHistory extends Command
{
    protected $signature = 'debug:approval-history {--user-id= : Specific user ID to check}';
    protected $description = 'Debug approval history data to diagnose empty history on hosting';

    public function handle()
    {
        $this->info('=== Debug Approval History ===');
        $this->newLine();

        // 1. Check table existence and record count
        $this->info('1. Table Check:');
        $tables = DB::select("SHOW TABLES LIKE '%approval%'");
        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            $count = DB::table($tableName)->count();
            $this->line("   - {$tableName}: {$count} records");
        }
        $this->newLine();

        // 2. Check document_approvals data
        $this->info('2. DocumentApproval Status Breakdown:');
        $statusCounts = DB::table('document_approvals')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
        foreach ($statusCounts as $row) {
            $this->line("   - {$row->status}: {$row->total}");
        }
        $this->newLine();

        // 3. Check specific user or all users with approvals
        $userId = $this->option('user-id');
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User ID {$userId} not found!");
                return 1;
            }
            $this->info("3. Checking user: {$user->name} (ID: {$user->id})");
            $this->checkUserApprovals($user);
        } else {
            $this->info('3. Users with approval records (as approver):');
            $approvers = DB::table('document_approvals')
                ->select('approver_id', DB::raw('count(*) as total'))
                ->groupBy('approver_id')
                ->get();

            foreach ($approvers as $approver) {
                $user = User::find($approver->approver_id);
                $name = $user ? $user->name : 'UNKNOWN';
                $this->line("   - User ID {$approver->approver_id} ({$name}): {$approver->total} approvals");

                // Check type
                $this->line("     approver_id type in DB: " . gettype($approver->approver_id));
            }
        }
        $this->newLine();

        // 4. Check for potential data issues
        $this->info('4. Potential Issues:');

        // Check for approvals with approved/rejected status
        $processedCount = DocumentApproval::whereIn('status', ['approved', 'rejected'])->count();
        $this->line("   - Processed approvals (approved/rejected): {$processedCount}");

        // Check for approvals without action_at
        $noActionAt = DocumentApproval::whereIn('status', ['approved', 'rejected'])
            ->whereNull('action_at')
            ->count();
        $this->line("   - Processed but no action_at: {$noActionAt}");

        // Check Eloquent model table name
        $model = new DocumentApproval();
        $this->line("   - Model table name: {$model->getTable()}");

        // 5. Check MySQL lower_case_table_names setting
        $this->newLine();
        $this->info('5. MySQL Settings:');
        $lctn = DB::select("SHOW VARIABLES LIKE 'lower_case_table_names'");
        if (!empty($lctn)) {
            $this->line("   - lower_case_table_names: {$lctn[0]->Value}");
        }

        $sqlMode = DB::select("SHOW VARIABLES LIKE 'sql_mode'");
        if (!empty($sqlMode)) {
            $this->line("   - sql_mode: {$sqlMode[0]->Value}");
        }

        $this->newLine();
        $this->info('=== Debug Complete ===');

        return 0;
    }

    private function checkUserApprovals(User $user): void
    {
        // Raw DB query
        $rawCount = DB::table('document_approvals')
            ->where('approver_id', $user->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->count();
        $this->line("   - Raw DB count (approved/rejected): {$rawCount}");

        // Eloquent query
        $eloquentCount = DocumentApproval::where('approver_id', $user->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->count();
        $this->line("   - Eloquent count (approved/rejected): {$eloquentCount}");

        // All statuses for this user
        $allStatuses = DB::table('document_approvals')
            ->where('approver_id', $user->id)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
        foreach ($allStatuses as $row) {
            $this->line("   - Status '{$row->status}': {$row->total}");
        }

        // Type check
        $this->line("   - User ID type: " . gettype($user->id) . " = {$user->id}");

        // Check a sample record
        $sample = DB::table('document_approvals')
            ->where('approver_id', $user->id)
            ->first();
        if ($sample) {
            $this->line("   - Sample record: ID={$sample->id}, status={$sample->status}, action_at={$sample->action_at}");
            $this->line("   - Sample approver_id type: " . gettype($sample->approver_id));
        } else {
            $this->warn("   - No approval records found for this user at all!");
        }
    }
}
