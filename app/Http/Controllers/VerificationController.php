<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Display document verification page (Public)
     */
    public function show(string $hash)
    {
        // Find document by approval hash
        $document = Document::where('approval_hash', $hash)->first();

        if (!$document) {
            return view('verify', [
                'valid' => false,
                'message' => 'Dokumen tidak ditemukan atau hash tidak valid.',
            ]);
        }

        // Load approvals with approver info
        $document->load(['user', 'approvals.approver', 'purchaseOrder', 'invoice']);

        // Determine verification status
        $isFullyApproved = in_array($document->status, ['approved', 'completed', 'paid']);

        // Get final approver (Presiden Direktur)
        $finalApproval = $document->approvals
            ->filter(fn($a) => str_contains(strtolower($a->approver_role ?? ''), 'presiden'))
            ->first();

        // Check if specific step or signer was scanned
        $scannedApproval = null;
        if (request()->filled('step')) {
            $stepVal = request('step');
            $scannedApproval = $document->approvals->first(function ($a) use ($stepVal) {
                return (string) $a->id === (string) $stepVal || (string) $a->approval_order === (string) $stepVal;
            });
        }

        $scannedSigner = request('signer'); // 'requester', 'mengetahui', 'finance'

        return view('verify', [
            'valid' => true,
            'document' => $document,
            'isFullyApproved' => $isFullyApproved,
            'finalApproval' => $finalApproval,
            'approvals' => $document->approvals->sortBy('approval_order'),
            'scannedApproval' => $scannedApproval,
            'scannedSigner' => $scannedSigner,
        ]);
    }
}
