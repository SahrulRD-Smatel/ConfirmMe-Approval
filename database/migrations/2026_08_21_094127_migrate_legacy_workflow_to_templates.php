<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\WorkflowChain;
use App\Models\DocumentTemplate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Copy workflow from global chains to existing templates
        $templates = DocumentTemplate::all();
        
        foreach ($templates as $template) {
            // Only update if workflow is currently empty
            if (empty($template->workflow)) {
                $approvalType = $template->approval_type;
                
                // Get legacy chain for this type
                $workflow = WorkflowChain::getChainForType($approvalType);
                
                // If DB workflow is empty, check config
                if (empty($workflow)) {
                    $typeWorkflows = config('workflow.types', []);
                    $workflow = $typeWorkflows[$approvalType] ?? null;
                }
                
                // If still empty, use hardcoded default from previous system
                if (empty($workflow)) {
                    $workflow = [1 => 'supervisor', 2 => 'direktur'];
                }
                
                // Save to template
                $template->workflow = $workflow;
                $template->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting this is not necessary/meaningful since it's just copying data
    }
};
