<x-app-layout>
    <x-slot name="title">{{ __('Review Dokumen - Executive') }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <a href="{{ route('executive.approvals') }}"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <span class="text-gray-400 dark:text-gray-500">|</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $document->document_number }}</span>
                </div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    {{ $document->title }}
                </h1>
            </div>
            @php
                $typeColors = [
                    'po' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                    'invoice' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
                    'loan' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                ];
                $typeLabels = ['po' => 'Purchase Order', 'invoice' => 'Invoice', 'loan' => 'Employee Loan'];
            @endphp
            <span
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $typeColors[$document->approval_type] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $typeLabels[$document->approval_type] ?? ucfirst($document->approval_type) }}
            </span>
        </div>
    </x-slot>

    {{-- 2FA will be triggered when user clicks approve/reject --}}

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Document Details --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft border border-gray-200/80 dark:border-gray-700/80">
                <div class="px-6 py-4 border-b border-gray-200/80 dark:border-gray-700/80">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Detail Dokumen') }}</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Pengaju') }}</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $document->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Departemen') }}</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $document->department ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Tanggal Pengajuan') }}</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $document->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Priority') }}</p>
                            @php
                                $priorityColors = [
                                    'urgent' => 'bg-red-100 text-red-800',
                                    'high' => 'bg-orange-100 text-orange-800',
                                    'normal' => 'bg-gray-100 text-gray-800',
                                    'low' => 'bg-blue-100 text-blue-800',
                                ];
                            @endphp
                            <span
                                class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ $priorityColors[$document->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($document->priority) }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200/80 dark:border-gray-700/80">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ __('Deskripsi') }}</p>
                        <p class="text-sm text-gray-900 dark:text-white">
                            {{ $document->description ?? __('Tidak ada deskripsi') }}</p>
                    </div>

                    <div class="pt-4 border-t border-gray-200/80 dark:border-gray-700/80">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ __('Nilai Dokumen') }}</p>
                        @php
                            $financeApproval = $document->approvals()
                                ->where('status', 'approved')
                                ->where('approver_role', 'finance')
                                ->whereNotNull('approved_amount')
                                ->first();
                            $budgetChanged = $financeApproval && (float) $financeApproval->approved_amount !== (float) $document->estimated_budget;
                        @endphp

                        @if($budgetChanged)
                            {{-- Show original amount with strikethrough --}}
                            <p class="text-lg text-gray-400 line-through">
                                Rp {{ number_format($document->estimated_budget ?? 0, 0, ',', '.') }}
                            </p>
                            {{-- Show Finance-approved amount highlighted --}}
                            <div class="flex items-center gap-2 mt-1">
                                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                    Rp {{ number_format($financeApproval->approved_amount, 0, ',', '.') }}
                                </p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    {{ __('Diubah oleh Finance') }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ __('Selisih') }}: Rp {{ number_format(abs($document->estimated_budget - $financeApproval->approved_amount), 0, ',', '.') }}
                                ({{ $financeApproval->approved_amount < $document->estimated_budget ? __('dikurangi') : __('ditambah') }})
                            </p>
                        @else
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                @if($document->approval_type === 'loan' && isset($document->metadata['loan_amount']))
                                    Rp {{ number_format($document->metadata['loan_amount'], 0, ',', '.') }}
                                @else
                                    Rp {{ number_format($document->estimated_budget ?? 0, 0, ',', '.') }}
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Attachments --}}
            @if($document->attachments->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft border border-gray-200/80 dark:border-gray-700/80">
                    <div class="px-6 py-4 border-b border-gray-200/80 dark:border-gray-700/80">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Lampiran') }}</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-2">
                            @foreach($document->attachments as $attachment)
                                <a href="{{ Storage::url($attachment->file_path) }}" target="_blank"
                                    class="flex items-center gap-3 p-3 rounded-xl hover:bg-primary-50/60 dark:hover:bg-gray-700">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $attachment->original_filename ?? $attachment->original_name ?? __('File') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $attachment->file_size_human ?? $attachment->human_file_size ?? '' }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Action Card --}}
            {{-- Action Card - 2FA will be checked in controller when submitting --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft border border-gray-200/80 dark:border-gray-700/80">
                    <div class="px-6 py-4 border-b border-gray-200/80 dark:border-gray-700/80">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Keputusan Anda') }}</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        {{-- Approve Form --}}
                        <form action="{{ route('executive.approvals.approve', $document) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Catatan (opsional)') }}</label>
                                <textarea name="comment" rows="2"
                                    class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-xl"
                                    placeholder="{{ __('Tambahkan catatan...') }}"></textarea>
                            </div>
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('Setujui Dokumen') }}
                            </button>
                        </form>

                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200/80 dark:border-gray-700/80"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white dark:bg-gray-800 text-gray-500">{{ __('atau') }}</span>
                            </div>
                        </div>

                        {{-- Reject Form --}}
                        <form action="{{ route('executive.approvals.reject', $document) }}" method="POST"
                            x-data="{ showReject: false }">
                            @csrf
                            <button type="button" @click="showReject = !showReject"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 font-medium rounded-xl"
                                x-show="!showReject">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                {{ __('Tolak Dokumen') }}
                            </button>
                            <div x-show="showReject" x-cloak class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Alasan penolakan') }} <span class="text-red-500">*</span></label>
                                    <textarea name="comment" rows="3" required
                                        class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-xl"
                                        placeholder="{{ __('Jelaskan alasan penolakan...') }}"></textarea>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" @click="showReject = false"
                                        class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-medium rounded-xl">
                                        {{ __('Batal') }}
                                    </button>
                                    <button type="submit"
                                        class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl">
                                        {{ __('Konfirmasi Tolak') }}
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200/80 dark:border-gray-700/80"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white dark:bg-gray-800 text-gray-500">{{ __('atau') }}</span>
                            </div>
                        </div>

                        {{-- Revise Form --}}
                        <form action="{{ route('executive.approvals.revise', $document) }}" method="POST"
                            x-data="{ showRevise: false }">
                            @csrf
                            <button type="button" @click="showRevise = !showRevise"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-amber-300 dark:border-amber-700 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 font-medium rounded-xl"
                                x-show="!showRevise">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                </svg>
                                {{ __('Minta Revisi') }}
                            </button>
                            <div x-show="showRevise" x-cloak class="space-y-3">
                                <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
                                    <p class="text-xs text-amber-800 dark:text-amber-300">
                                        {{ __('Dokumen akan dikembalikan ke pengaju untuk diperbaiki sesuai catatan Anda.') }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Catatan revisi') }} <span class="text-red-500">*</span></label>
                                    <textarea name="comment" rows="3" required
                                        class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-xl"
                                        placeholder="{{ __('Tuliskan apa yang perlu diperbaiki...') }}"></textarea>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" @click="showRevise = false"
                                        class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-medium rounded-xl">
                                        {{ __('Batal') }}
                                    </button>
                                    <button type="submit"
                                        class="flex-1 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-xl">
                                        {{ __('Konfirmasi Revisi') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


            {{-- Approval Trail --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft border border-gray-200/80 dark:border-gray-700/80">
                <div class="px-6 py-4 border-b border-gray-200/80 dark:border-gray-700/80">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Approval Trail</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($document->approvalLogs->take(10) as $log)
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                                        {{ $log->action === 'approved' ? 'bg-emerald-100 dark:bg-emerald-900/30' : '' }}
                                        {{ $log->action === 'rejected' ? 'bg-red-100 dark:bg-red-900/30' : '' }}
                                        {{ !in_array($log->action, ['approved', 'rejected']) ? 'bg-gray-100 dark:bg-gray-700' : '' }}
                                    ">
                                    @if($log->action === 'approved')
                                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    @elseif($log->action === 'rejected')
                                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $log->user->name ?? 'System' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $log->description }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                        {{ $log->created_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">{{ __('Belum ada aktivitas') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>