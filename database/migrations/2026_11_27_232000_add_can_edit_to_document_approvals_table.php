<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('document_approvals', 'can_edit')) {
            Schema::table('document_approvals', function (Blueprint $table) {
                $table->boolean('can_edit')->default(false)->after('pdf_label');
            });
        }

        // Initialize can_edit for existing HR_UMBPD (Pemesanan Tiket & Permintaan Uang Muka) template
        try {
            $template = \App\Models\DocumentTemplate::where('code', 'HR_UMBPD')->first();
            if ($template) {
                $fields = $template->fields ?? [];
                $workflow = array_values((array) ($template->workflow ?? []));
                $signers = $fields['workflow_signers'] ?? [];

                foreach ($workflow as $idx => $step) {
                    $stepStr = is_array($step) ? ($step['target'] ?? '') : (string)$step;
                    if (!isset($signers[$idx])) {
                        $signers[$idx] = [
                            'show_in_pdf' => true,
                            'pdf_label' => null,
                            'can_edit' => false,
                        ];
                    }
                    // If step is index 1 (2nd step: Supervisor HR / Tiket)
                    if ($idx === 1) {
                        $signers[$idx]['can_edit'] = true;
                    } else {
                        $signers[$idx]['can_edit'] = $signers[$idx]['can_edit'] ?? false;
                    }
                }
                $fields['workflow_signers'] = $signers;
                $template->fields = $fields;
                $template->save();

                // Also update any existing in-progress document approvals for HR_UMBPD step 2
                $docs = \App\Models\Document::where('template', 'HR_UMBPD')->pluck('id');
                if ($docs->isNotEmpty()) {
                    \App\Models\DocumentApproval::whereIn('document_id', $docs)
                        ->where('approval_order', 2)
                        ->update(['can_edit' => true]);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning("Could not auto-populate can_edit for HR_UMBPD: " . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('document_approvals', 'can_edit')) {
            Schema::table('document_approvals', function (Blueprint $table) {
                $table->dropColumn('can_edit');
            });
        }
    }
};
