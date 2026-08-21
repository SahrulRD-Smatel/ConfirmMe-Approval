@props([
    'type' => 'only', // only, po, invoice, loan
    'document' => null,
    'approvals' => collect(),
])

@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;

    // Determine requester's actual role title
    $requester = $document?->user;
    $requesterTitle = 'Staff'; // default
    if ($requester) {
        if ($requester->hasRole('presiden_direktur')) {
            $requesterTitle = 'Presiden Direktur';
        } elseif ($requester->hasAnyRole(['direktur_hr', 'direktur_finance', 'direktur_it', 'direktur_sales'])) {
            $requesterTitle = 'Direktur';
        } elseif ($requester->hasRole('manager')) {
            $requesterTitle = 'Manager';
        } elseif ($requester->hasRole('supervisor')) {
            $requesterTitle = 'Supervisor';
        } elseif ($requester->hasRole('finance')) {
            $requesterTitle = 'Finance';
        } elseif ($requester->hasRole('hrd')) {
            $requesterTitle = 'HRD';
        }
    }

    // Define all possible signature columns based on document type
    $allColumns = match($type) {
        'only' => [
            ['role' => 'requester', 'label' => 'Dibuat', 'title' => $requesterTitle],
            ['role' => 'supervisor', 'label' => 'Verified', 'title' => 'Supervisor'],
            ['role' => 'manager', 'label' => 'Approved', 'title' => 'Manager'],
        ],
        'po' => [
            ['role' => 'requester', 'label' => 'Dibuat', 'title' => $requesterTitle],
            ['role' => 'supervisor', 'label' => 'Verified', 'title' => 'Supervisor'],
            ['role' => 'manager', 'label' => 'Reviewed', 'title' => 'Manager'],
            ['role' => 'direktur', 'label' => 'Authorized', 'title' => 'Director'],
            ['role' => 'finance', 'label' => 'Budget Check', 'title' => 'Finance'],
            ['role' => 'presiden_direktur', 'label' => 'Final Approval', 'title' => 'President Director'],
        ],
        'invoice' => [
            ['role' => 'requester', 'label' => 'Dibuat', 'title' => $requesterTitle],
            ['role' => 'supervisor', 'label' => 'Verified', 'title' => 'Supervisor'],
            ['role' => 'manager', 'label' => 'Reviewed', 'title' => 'Manager'],
            ['role' => 'finance', 'label' => 'Budget Check', 'title' => 'Finance'],
            ['role' => 'direktur', 'label' => 'Authorized', 'title' => 'Director'],
            ['role' => 'presiden_direktur', 'label' => 'Final Approval', 'title' => 'President Director'],
        ],
        'loan' => [
            ['role' => 'requester', 'label' => 'Pemohon', 'title' => $requesterTitle],
            ['role' => 'hrd', 'label' => 'HRD Check', 'title' => 'HR Manager'],
            ['role' => 'finance', 'label' => 'Finance Check', 'title' => 'Finance'],
            ['role' => 'direktur_hr', 'label' => 'Authorized', 'title' => 'Direktur HR'],
            ['role' => 'presiden_direktur', 'label' => 'Final Approval', 'title' => 'Presiden Direktur'],
        ],
        default => [],
    };
    
    // Get presiden direktur name
    $presdir = \App\Models\User::role('presiden_direktur')->first();
    $presdirName = $presdir?->name ?? 'Presiden Direktur';

    // Helper function to find approval by role
    $findApprovalByRole = function($role) use ($approvals) {
        return $approvals->filter(function($a) use ($role) {
            $approverRole = strtolower($a->approver_role ?? '');
            $searchRole = strtolower($role);
            
            if ($searchRole === 'direktur') {
                return str_starts_with($approverRole, 'direktur_') || $approverRole === 'direktur';
            }
            
            return $approverRole === $searchRole;
        })->first();
    };

    // Filter columns: only show steps that have actual approval records (or are 'requester')
    // This ensures skipped workflow steps are not displayed
    $columns = collect($allColumns)->filter(function($col) use ($findApprovalByRole) {
        if ($col['role'] === 'requester') return true;
        return $findApprovalByRole($col['role']) !== null;
    })->values()->all();

    // Determine the last approval column label dynamically
    // The last non-requester column should be labeled "Approved" for clarity
    $lastIndex = count($columns) - 1;
    if ($lastIndex > 0 && $columns[$lastIndex]['role'] !== 'requester') {
        $columns[$lastIndex]['label'] = ($type === 'loan') ? 'Final Approval' : 'Approved';
    }

    // Check if document is fully approved
    $isFullyApproved = $document && in_array($document->status, ['approved', 'completed', 'paid', 'waiting_disbursement']);
    
    // Generate verification URL
    $verificationUrl = $document?->approval_hash ? route('verify.show', $document->approval_hash) : null;
@endphp

<div class="approval-block mt-8 print:mt-4">
    <h3 class="text-sm font-semibold text-gray-700 mb-4 print:text-black flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        Digital Approval Block
    </h3>
    
    <div class="overflow-x-auto rounded-2xl border border-gray-200/80 bg-white p-2 shadow-sm">
        <table class="min-w-[680px] w-full border-collapse overflow-hidden rounded-xl border border-gray-200 text-xs">
            <thead>
                <tr>
                    @foreach($columns as $col)
                        <th class="border border-gray-200 px-2 py-2 text-center bg-gray-50/80 font-semibold print:bg-white" style="min-width: 100px;">
                            {{ $col['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                {{-- QR Code / Status Row --}}
                <tr>
                    @foreach($columns as $col)
                        <td class="border border-gray-200 px-2 py-3 text-center align-middle" style="height: 100px;">
                            @php
                                $approval = $findApprovalByRole($col['role']);
                                $isApproved = $approval && $approval->status === 'approved';
                                $isRequester = $col['role'] === 'requester';
                            @endphp
                            
                            @if($isRequester)
                                {{-- Requester always shows checkmark --}}
                                <div class="flex flex-col items-center justify-center h-full">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-1">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <span class="text-[9px] text-green-600 font-medium">Created</span>
                                </div>
                            @elseif($isApproved && $approval->action_at)
                                {{-- Approved - Show QR Code --}}
                                <div class="flex flex-col items-center justify-center h-full">
                                    @if($verificationUrl && ($col['role'] === 'presiden_direktur' ? $isFullyApproved : true))
                                        <div class="bg-white p-1 rounded border border-gray-200">
                                            {!! QrCode::size(50)->generate($verificationUrl) !!}
                                        </div>
                                    @else
                                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <span class="text-[9px] text-green-600 font-medium mt-1">Signed</span>
                                </div>
                            @else
                                {{-- Pending --}}
                                <div class="flex flex-col items-center justify-center h-full">
                                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <span class="text-[9px] text-gray-400 font-medium mt-1">Pending</span>
                                </div>
                            @endif
                        </td>
                    @endforeach
                </tr>

                {{-- Digital Signature Info Row --}}
                <tr>
                    @foreach($columns as $col)
                        <td class="border border-gray-200 px-2 py-2 text-center bg-gray-50/80 print:bg-white">
                            @php
                                $approval = $findApprovalByRole($col['role']);
                                $isApproved = $approval && $approval->status === 'approved';
                            @endphp
                            
                            @if($col['role'] !== 'requester' && $isApproved)
                                <p class="text-[8px] text-gray-500 italic">Digitally Signed by:</p>
                            @elseif($col['role'] === 'requester')
                                <p class="text-[8px] text-gray-500 italic">Submitted by:</p>
                            @endif
                        </td>
                    @endforeach
                </tr>

                {{-- Name Row --}}
                <tr>
                    @foreach($columns as $col)
                        <td class="border border-gray-200 px-2 py-1 text-center">
                            @php
                                $approval = $findApprovalByRole($col['role']);
                                $name = match($col['role']) {
                                    'requester' => $document?->user?->name ?? '(Nama Staff)',
                                    'presiden_direktur' => $presdirName,
                                    default => $approval?->approver?->name ?? ('(' . $col['title'] . ')')
                                };
                            @endphp
                            <span class="font-bold text-gray-900 underline">{{ $name }}</span>
                        </td>
                    @endforeach
                </tr>

                {{-- Title Row --}}
                <tr>
                    @foreach($columns as $col)
                        <td class="border border-gray-200 px-1 py-1 text-center text-gray-600">
                            {{ $col['title'] }}
                        </td>
                    @endforeach
                </tr>

                {{-- Date & Time Row --}}
                <tr>
                    @foreach($columns as $col)
                        <td class="border border-gray-200 px-1 py-1 text-center text-gray-500">
                            @php
                                $approval = $findApprovalByRole($col['role']);
                                if ($col['role'] === 'requester') {
                                    $datetime = $document?->created_at;
                                } else {
                                    $datetime = $approval?->action_at;
                                }
                            @endphp
                            @if($datetime)
                                <div class="text-[10px]">{{ $datetime->format('d/m/Y') }}</div>
                                <div class="text-[9px] text-gray-400">{{ $datetime->format('H:i') }} WIB</div>
                            @else
                                <div class="text-[10px] text-gray-400">-</div>
                            @endif
                        </td>
                    @endforeach
                </tr>

                {{-- IP Address Row (optional, for audit) --}}
                <tr class="print:hidden">
                    @foreach($columns as $col)
                        <td class="border border-gray-200 px-1 py-1 text-center text-gray-400 text-[8px]">
                            @php
                                $approval = $findApprovalByRole($col['role']);
                            @endphp
                            @if($col['role'] !== 'requester' && $approval?->ip_address)
                                IP: {{ $approval->ip_address }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Verification Notice --}}
    @if($isFullyApproved && $verificationUrl)
        <div class="mt-4 p-3 rounded-2xl border border-success-200 bg-success-50/80 flex items-center gap-3 print:bg-white print:border-green-400">
            <div class="flex-shrink-0">
                {!! QrCode::size(60)->generate($verificationUrl) !!}
            </div>
            <div class="text-xs">
                <p class="font-semibold text-green-800">✓ Dokumen Sah - Terverifikasi Digital</p>
                <p class="text-green-600 mt-1">Scan QR Code untuk verifikasi keaslian dokumen.</p>
                <p class="text-green-500 mt-1 text-[10px] break-all">{{ $verificationUrl }}</p>
            </div>
        </div>
    @endif
</div>
