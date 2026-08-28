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

    $columns = [];
    
    // Always start with Requester
    $columns[] = [
        'is_requester' => true,
        'label' => 'Dibuat',
        'title' => $requesterTitle,
        'approval' => null,
    ];

    // Add actual workflow steps from the database dynamically
    if ($approvals) {
        $totalApprovals = count($approvals);
        $index = 0;
        foreach($approvals as $approval) {
            // Determine label
            $isLast = ($index === $totalApprovals - 1);
            $label = $isLast ? (($type === 'loan') ? 'Final Approval' : 'Approved') : 'Reviewed';
            
            // Format role name for display
            $roleName = $approval->role_label ?? 'Approver';

            $columns[] = [
                'is_requester' => false,
                'label' => $label,
                'title' => $roleName,
                'approval' => $approval,
            ];
            $index++;
        }
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
                                $isRequester = $col['is_requester'];
                                $approval = $col['approval'];
                                $isApproved = $approval && $approval->status === 'approved';
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
                                    @php
                                        $isLastStep = ($loop->index === count($columns) - 1);
                                        $showQr = $verificationUrl && ($isLastStep ? $isFullyApproved : true);
                                    @endphp
                                    @if($showQr)
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
                                $isRequester = $col['is_requester'];
                                $approval = $col['approval'];
                                $isApproved = $approval && $approval->status === 'approved';
                            @endphp
                            
                            @if(!$isRequester && $isApproved)
                                <p class="text-[8px] text-gray-500 italic">Digitally Signed by:</p>
                            @elseif($isRequester)
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
                                $isRequester = $col['is_requester'];
                                $approval = $col['approval'];
                                $name = $isRequester ? ($document?->user?->name ?? '(Nama Staff)') : ($approval?->approver?->name ?? ('(' . $col['title'] . ')'));
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
                                $isRequester = $col['is_requester'];
                                $approval = $col['approval'];
                                if ($isRequester) {
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
                                $isRequester = $col['is_requester'];
                                $approval = $col['approval'];
                            @endphp
                            @if(!$isRequester && $approval?->ip_address)
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
