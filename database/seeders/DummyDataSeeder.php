<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Document;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Invoice;
use App\Models\EmployeeLoan;
use App\Models\LeaveRequest;
use App\Models\Attendance;
use App\Models\OfficeLocation;
use App\Models\DocumentTemplate;
use App\Models\Budget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        // Ensure we have users
        $users = User::all();
        if ($users->count() < 2) {
            $this->command->info("Tolong jalankan php artisan db:seed (DatabaseSeeder) terlebih dahulu untuk membuat User.");
            return;
        }

        $creator = User::where('email', 'admin@confirmme.com')->first() ?? $users->first();
        $employee = User::where('email', 'staff.it@confirmme.com')->first() ?? $users->last();
        $approver = User::where('email', 'manager.it@confirmme.com')->first() ?? $users->where('id', '!=', $creator->id)->first();

        // Ensure Office Location
        $office = OfficeLocation::firstOrCreate(
            ['name' => 'Kantor Pusat Jakarta'],
            [
                'address' => 'Jl. Sudirman No. 1, Jakarta',
                'latitude' => -6.200000,
                'longitude' => 106.816666,
                'radius' => 100,
                'is_active' => true
            ]
        );

        // Ensure Document Templates
        $poTemplate = DocumentTemplate::where('approval_type', 'po')->first();
        $invoiceTemplate = DocumentTemplate::where('approval_type', 'invoice')->first();
        $loanTemplate = DocumentTemplate::where('approval_type', 'loan')->first();
        $leaveTemplate = DocumentTemplate::where('approval_type', 'only')->where('code', 'LEAVE_REQUEST')->first();

        $budget = Budget::first();

        $companies = ['PT. Teknologi Jaya', 'CV. Makmur Sentosa', 'PT. Solusi Data', 'CV. Abadi Berkah', 'PT. Nusantara Sakti'];
        $items = ['Laptop', 'Mouse', 'Keyboard', 'Monitor', 'Printer', 'Tinta', 'Kertas HVS', 'Meja Kerja'];

        // Counters for manual number generation
        $docCount = 1;
        $poCount = 1;
        $invCount = 1;
        $loanCount = 1;

        // Generate 15 Dummy Documents
        for ($i = 0; $i < 15; $i++) {
            $typeOptions = ['po', 'invoice', 'loan', 'leave'];
            $selectedType = $typeOptions[array_rand($typeOptions)];
            
            $statusOptions = ['draft', 'pending', 'approved', 'rejected', 'completed'];
            $status = $statusOptions[array_rand($statusOptions)];
            
            $date = Carbon::now()->subDays(rand(1, 60));
            $yearMonth = $date->format('Ym');
            
            $docAmount = rand(10, 500) * 100000; // 1M to 50M
            $company = $companies[array_rand($companies)];

            $template = match($selectedType) {
                'po' => $poTemplate,
                'invoice' => $invoiceTemplate,
                'loan' => $loanTemplate,
                'leave' => $leaveTemplate,
                default => $poTemplate
            };

            if (!$template) continue;

            $document = Document::create([
                'document_number' => sprintf('DOC-%s-%04d', $yearMonth, $docCount++),
                'title' => 'Pengajuan ' . $template->name . ' - ' . $company,
                'description' => 'Ini adalah pengajuan dummy otomatis ' . Str::random(10),
                'approval_type' => $template->approval_type,
                'po_category' => 'internal',
                'status' => $status,
                'estimated_budget' => $docAmount,
                'approved_budget' => in_array($status, ['approved', 'completed']) ? $docAmount : null,
                'priority' => ['low', 'normal', 'high', 'urgent'][array_rand(['low', 'normal', 'high', 'urgent'])],
                'user_id' => $creator->id,
                'current_approver_id' => in_array($status, ['pending']) ? $approver->id : null,
                'department' => 'IT',
                'template' => $template->code,
                'submitted_at' => $status != 'draft' ? $date : null,
                'approved_at' => in_array($status, ['approved', 'completed']) ? $date->copy()->addDays(2) : null,
            ]);
            
            $document->created_at = $date;
            $document->updated_at = $date;
            $document->save(['timestamps' => false]);

            // Sync with other tables based on type
            if ($selectedType == 'po' && in_array($status, ['approved', 'completed'])) {
                $po = PurchaseOrder::create([
                    'po_number' => sprintf('PO-%s-%04d', $yearMonth, $poCount++),
                    'document_id' => $document->id,
                    'vendor_name' => $company,
                    'vendor_address' => 'Jl. Sudirman No ' . rand(1, 100),
                    'vendor_phone' => '08' . rand(100000000, 999999999),
                    'vendor_email' => 'vendor' . rand(1, 100) . '@example.com',
                    'order_date' => $document->approved_at,
                    'expected_delivery_date' => $document->approved_at->copy()->addDays(7),
                    'subtotal' => $docAmount,
                    'tax_amount' => $docAmount * 0.11,
                    'total_amount' => $docAmount * 1.11,
                    'status' => $status == 'completed' ? 'completed' : 'issued',
                    'created_by' => $creator->id,
                ]);
                $po->created_at = $date;
                $po->updated_at = $date;
                $po->save(['timestamps' => false]);

                // Create PO Items
                for ($j = 0; $j < rand(1, 3); $j++) {
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $po->id,
                        'item_name' => $items[array_rand($items)] . ' ' . Str::random(3),
                        'description' => 'Spesifikasi standar',
                        'quantity' => rand(1, 10),
                        'unit_price' => $docAmount / 2,
                        'total_price' => $docAmount,
                    ]);
                }

                // If PO is completed, sometimes create Invoice
                if ($status == 'completed' && rand(0, 1)) {
                    $inv = Invoice::create([
                        'invoice_number' => sprintf('INV-%s-%04d', $yearMonth, $invCount++),
                        'document_id' => $document->id,
                        'purchase_order_id' => $po->id,
                        'vendor_name' => $po->vendor_name,
                        'vendor_address' => $po->vendor_address,
                        'invoice_date' => $po->expected_delivery_date,
                        'due_date' => $po->expected_delivery_date->copy()->addDays(14),
                        'subtotal' => $po->subtotal,
                        'tax_amount' => $po->tax_amount,
                        'total_amount' => $po->total_amount,
                        'paid_amount' => rand(0, 1) ? $po->total_amount : 0,
                        'status' => rand(0, 1) ? 'paid' : 'issued',
                        'created_by' => $creator->id,
                    ]);
                    $inv->created_at = $date;
                    $inv->updated_at = $date;
                    $inv->save(['timestamps' => false]);
                }

            } elseif ($selectedType == 'invoice' && in_array($status, ['approved', 'completed'])) {
                $inv = Invoice::create([
                    'invoice_number' => sprintf('INV-%s-%04d', $yearMonth, $invCount++),
                    'document_id' => $document->id,
                    'vendor_name' => $company,
                    'vendor_address' => 'Jl. Thamrin No ' . rand(1, 100),
                    'invoice_date' => $document->approved_at,
                    'due_date' => $document->approved_at->copy()->addDays(30),
                    'subtotal' => $docAmount,
                    'tax_amount' => $docAmount * 0.11,
                    'total_amount' => $docAmount * 1.11,
                    'paid_amount' => $status == 'completed' ? ($docAmount * 1.11) : 0,
                    'status' => $status == 'completed' ? 'paid' : 'issued',
                    'created_by' => $creator->id,
                ]);
                $inv->created_at = $date;
                $inv->updated_at = $date;
                $inv->save(['timestamps' => false]);

            } elseif ($selectedType == 'loan' && in_array($status, ['approved', 'completed'])) {
                $loan = EmployeeLoan::create([
                    'loan_number' => sprintf('LOAN-%s-%04d', $yearMonth, $loanCount++),
                    'document_id' => $document->id,
                    'employee_id' => $employee->id,
                    'loan_amount' => $docAmount,
                    'installment_months' => 12,
                    'monthly_installment' => $docAmount / 12,
                    'total_paid' => $status == 'completed' ? $docAmount : 0,
                    'outstanding_amount' => $status == 'completed' ? 0 : $docAmount,
                    'status' => $status == 'completed' ? 'paid_off' : 'disbursed',
                    'disbursement_date' => $document->approved_at,
                    'bank_account_name' => $employee->name,
                    'bank_account_number' => '123' . rand(1000000, 9999999),
                    'bank_name' => 'BCA',
                    'created_by' => $creator->id,
                ]);
                $loan->created_at = $date;
                $loan->updated_at = $date;
                $loan->save(['timestamps' => false]);

            } elseif ($selectedType == 'leave' && in_array($status, ['approved', 'completed'])) {
                $leave = LeaveRequest::create([
                    'user_id' => $employee->id,
                    'type' => ['annual', 'sick', 'unpaid', 'other'][array_rand(['annual', 'sick', 'unpaid', 'other'])],
                    'start_date' => $document->approved_at,
                    'end_date' => $document->approved_at->copy()->addDays(rand(1, 3)),
                    'reason' => 'Keperluan keluarga',
                    'status' => 'approved',
                    'current_step' => 3,
                    'approved_by' => $approver->id,
                    'approved_at' => $document->approved_at,
                ]);
                $leave->created_at = $date;
                $leave->updated_at = $date;
                $leave->save(['timestamps' => false]);
            }
        }

        // Generate Attendance Data for the last 15 days
        for ($i = 15; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // Skip weekends usually
            if ($date->isWeekend() && rand(0, 10) > 2) continue;

            $types = ['check_in', 'check_out'];
            foreach ($types as $type) {
                // Sometime user forgets to checkout
                if ($type == 'check_out' && rand(0, 10) > 8) continue;

                $time = $type == 'check_in' 
                        ? $date->copy()->setHour(rand(7, 9))->setMinute(rand(0, 59))
                        : $date->copy()->setHour(rand(17, 19))->setMinute(rand(0, 59));

                Attendance::create([
                    'user_id' => $employee->id,
                    'date' => $date->format('Y-m-d'),
                    'type' => $type,
                    'latitude' => $office->latitude + (rand(-100, 100) / 100000),
                    'longitude' => $office->longitude + (rand(-100, 100) / 100000),
                    'location_name' => $office->name,
                    'office_location_id' => $office->id,
                    'status' => $type == 'check_in' && $time->hour >= 9 ? 'late' : 'on_time',
                    'is_wfh' => rand(0, 10) > 8,
                    'created_at' => $time,
                    'updated_at' => $time,
                ]);
            }
        }
        
        $this->command->info('15 Dummy Documents (with sync to PO, Invoice, Loan, Leave) and 15 days of Attendance data created successfully!');
    }
}
