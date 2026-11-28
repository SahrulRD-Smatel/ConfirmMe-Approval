<?php

namespace App\Console\Commands;

use App\Models\EmployeeLoan;
use Illuminate\Console\Command;

class SyncEmployeeLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loan:sync {--all : Sync all loans, not just zero-amount loans} {--id= : Specific loan ID to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize and recalculate employee loans from dynamic document metadata';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $id = $this->option('id');
        $all = $this->option('all');

        $query = EmployeeLoan::whereNotNull('document_id');

        if ($id) {
            $query->where('id', $id);
        } elseif (!$all) {
            $query->where('loan_amount', '<=', 0);
        }

        $loans = $query->get();

        if ($loans->isEmpty()) {
            $this->info('Tidak ada data pinjaman yang perlu disinkronkan.');
            return 0;
        }

        $this->info("Menemukan {$loans->count()} data pinjaman untuk disinkronkan...");

        $synced = 0;
        $failed = 0;

        foreach ($loans as $loan) {
            $oldAmount = $loan->loan_amount;
            $oldMonths = $loan->installment_months;

            if ($loan->syncFromDocument()) {
                $loan->refresh();
                $this->line(sprintf(
                    '[%s] Berhasil: Rp %s -> Rp %s (%d bln -> %d bln)',
                    $loan->loan_number,
                    number_format($oldAmount, 0, ',', '.'),
                    number_format($loan->loan_amount, 0, ',', '.'),
                    $oldMonths,
                    $loan->installment_months
                ));
                $synced++;
            } else {
                $this->warn("[{$loan->loan_number}] Gagal disinkronkan (dokumen tidak ditemukan atau data kosong).");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Sinkronisasi selesai! {$synced} berhasil, {$failed} gagal.");

        return 0;
    }
}
