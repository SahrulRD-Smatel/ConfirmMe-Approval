<x-app-layout>
    <x-slot name="title">{{ __('Review') }}: {{ $document->document_number }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('approvals.index') }}"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                        {{ $document->document_number }}
                    </h1>
                    <x-ui.badge :status="$document->priority_color" size="lg">
                        {{ $document->priority_label }}
                    </x-ui.badge>
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Review dan berikan keputusan approval') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <!-- Main Content -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Document Info -->
            <x-ui.card>
                <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ $document->title }}</h2>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Jenis Approval') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $document->approval_type_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Pengaju') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $document->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Departemen') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $document->department ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Tanggal Pengajuan') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $document->submitted_at?->format('d F Y H:i') ?? $document->created_at->format('d F Y H:i') }}
                        </dd>
                    </div>
                    @if((float) $document->estimated_budget > 0)
                        <div class="sm:col-span-2">
                            @php
                                $financeApproval = $document->approvals()
                                    ->where('status', 'approved')
                                    ->where('approver_role', 'finance')
                                    ->whereNotNull('approved_amount')
                                    ->first();
                                $budgetChanged = $financeApproval && (float) $financeApproval->approved_amount !== (float) $document->estimated_budget;
                            @endphp
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Estimasi Anggaran') }}</dt>
                            @if($budgetChanged)
                                <dd class="mt-1 text-base text-gray-400 line-through">Rp
                                    {{ number_format($document->estimated_budget, 0, ',', '.') }}
                                </dd>
                                <dd class="mt-1 flex items-center gap-2">
                                    <span class="text-xl font-bold text-primary-600 dark:text-primary-400">Rp
                                        {{ number_format($financeApproval->approved_amount, 0, ',', '.') }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        {{ __('Diubah oleh Finance') }}
                                    </span>
                                </dd>
                            @else
                                <dd class="mt-1 text-xl font-bold text-gray-900 dark:text-white">Rp
                                    {{ number_format($document->estimated_budget, 0, ',', '.') }}
                                </dd>
                            @endif
                        </div>
                    @endif
                </dl>

                <div class="border-t border-gray-200/80 dark:border-gray-700/80 pt-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('Deskripsi') }}</h3>
                    <div class="prose prose-sm dark:prose-invert max-w-none">
                        {!! nl2br(e($document->description)) !!}
                    </div>
                </div>
            </x-ui.card>

            <!-- Type-Specific Details & Dynamic Fields -->
            <x-document.type-details :document="$document" />
            <x-document.dynamic-fields :document="$document" />

            <!-- Attachments -->
            @if($document->attachments->count() > 0)
                <x-ui.card>
                    <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Lampiran') }}
                        ({{ $document->attachments->count() }})</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($document->attachments as $attachment)
                            <a href="{{ $attachment->url }}" target="_blank"
                                class="flex items-center gap-3 p-3 bg-gray-50/80 dark:bg-gray-800 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <div
                                    class="flex-shrink-0 w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                    <x-icon name="document-text" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $attachment->original_filename }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $attachment->file_size_human }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </x-ui.card>
            @endif

            <!-- Previous Approvals -->
            @php
                $previousApprovals = $document->approvals->where('status', '!=', 'pending');
            @endphp
            @if($previousApprovals->count() > 0)
                <x-ui.card>
                    <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Approval Sebelumnya') }}</h2>
                    <div class="space-y-3">
                        @foreach($previousApprovals as $approval)
                            <div
                                class="flex items-start gap-3 p-3 rounded-xl {{ $approval->status === 'approved' ? 'bg-success-50 dark:bg-success-900/20' : 'bg-danger-50 dark:bg-danger-900/20' }}">
                                <div
                                    class="flex-shrink-0 w-10 h-10 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center">
                                    @if($approval->status === 'approved')
                                        <x-icon name="check-circle" class="w-6 h-6 text-success-600" />
                                    @else
                                        <x-icon name="x-circle" class="w-6 h-6 text-danger-600" />
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $approval->approver->name }}
                                        </p>
                                        <span
                                            class="text-xs text-gray-500">{{ $approval->action_at?->format('d M Y H:i') }}</span>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $approval->approver?->position ?? $approval->role_label }}</p>
                                    @if($approval->comment)
                                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-2 italic">"{{ $approval->comment }}"
                                        </p>
                                    @endif
                                    @if($approval->approved_amount)
                                        <p class="text-sm text-primary-600 dark:text-primary-400 mt-1 font-medium flex items-center gap-1.5">
                                            <x-icon name="banknotes" class="w-4 h-4 shrink-0" />
                                            <span>{{ __('Nominal disetujui') }}: Rp {{ number_format($approval->approved_amount, 0, ',', '.') }}</span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            @endif
        </div>

        <!-- Approval Action Sidebar -->
        <div class="space-y-6">
            <!-- Approval Form -->
            <x-ui.card>
                <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Keputusan Anda') }}</h2>

                <div x-data="{ 
                    action: null, 
                    showConfirmModal: false,
                    approvedAmount: {{ 
                        $document->approvals()
                            ->where('approver_role', 'finance')
                            ->where('status', 'approved')
                            ->whereNotNull('approved_amount')
                            ->value('approved_amount') ?? $document->estimated_budget ?? 0 
                    }},
                    approveComment: '',
                    rejectComment: '',
                    reviseComment: ''
                }" class="space-y-4">
                    <!-- Action Buttons -->
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" @click="action = 'approve'"
                            :class="action === 'approve' ? 'ring-2 ring-success-500 bg-success-50 dark:bg-success-900/30' : 'bg-gray-50 dark:bg-gray-800'"
                            class="flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200/80 dark:border-gray-700/80 hover:border-success-300 transition-all">
                            <x-icon name="check-circle" class="w-8 h-8 text-success-600 mb-2" />
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Setujui') }}</span>
                        </button>

                        <button type="button" @click="action = 'revise'"
                            :class="action === 'revise' ? 'ring-2 ring-amber-500 bg-amber-50 dark:bg-amber-900/30' : 'bg-gray-50 dark:bg-gray-800'"
                            class="flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200/80 dark:border-gray-700/80 hover:border-amber-300 transition-all">
                            <x-icon name="pencil-square" class="w-8 h-8 text-amber-600 mb-2" />
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Revisi') }}</span>
                        </button>

                        <button type="button" @click="action = 'reject'"
                            :class="action === 'reject' ? 'ring-2 ring-danger-500 bg-danger-50 dark:bg-danger-900/30' : 'bg-gray-50 dark:bg-gray-800'"
                            class="flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200/80 dark:border-gray-700/80 hover:border-danger-300 transition-all">
                            <x-icon name="x-circle" class="w-8 h-8 text-danger-600 mb-2" />
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Tolak') }}</span>
                        </button>
                    </div>

                    <!-- Approve Form -->
                    <div x-show="action === 'approve'" x-transition class="space-y-4">
                        @if((float) $document->estimated_budget > 0)
                            <div>
                                {{-- Any approver can edit the approved amount in the dynamic system --}}
                                <label for="approved_amount"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Jumlah Disetujui') }}
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                                    <input type="text" id="approved_amount"
                                        value="{{ $document->estimated_budget ?? 0 }}"
                                        class="currency-input block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10"
                                        inputmode="numeric"
                                        @currency-change="approvedAmount = $event.detail.value">
                                </div>
                            </div>
                        @endif

                        <div>
                            <label for="approve_comment"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('Catatan (Opsional)') }}
                            </label>
                            <textarea x-model="approveComment" id="approve_comment" rows="3"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10"
                                placeholder="{{ __('Tambahkan catatan...') }}"></textarea>
                        </div>

                        <button type="button" @click="showConfirmModal = true"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-success-600 hover:bg-success-700 text-white font-medium rounded-xl transition-colors">
                            <x-icon name="check-circle" class="w-5 h-5" />
                            {{ __('Lanjutkan Persetujuan') }}
                        </button>
                    </div>

                    <!-- Reject Form -->
                    <div x-show="action === 'reject'" x-transition class="space-y-4">
                        <div>
                            <label for="reject_comment"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('Alasan Penolakan') }} <span class="text-danger-500">*</span>
                            </label>
                            <textarea x-model="rejectComment" id="reject_comment" rows="3"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10"
                                placeholder="{{ __('Jelaskan alasan penolakan...') }}"></textarea>
                        </div>

                        <button type="button"
                            @click="rejectComment.trim() ? showConfirmModal = true : showAlert('{{ __('Alasan penolakan wajib diisi') }}')"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-danger-600 hover:bg-danger-700 text-white font-medium rounded-xl transition-colors">
                            <x-icon name="x-circle" class="w-5 h-5" />
                            {{ __('Lanjutkan Penolakan') }}
                        </button>
                    </div>

                    <!-- Revise Form -->
                    <div x-show="action === 'revise'" x-transition class="space-y-4">
                        <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
                            <p class="text-sm text-amber-800 dark:text-amber-300">
                                <x-icon name="information-circle" class="w-4 h-4 inline" />
                                {{ __('Dokumen akan dikembalikan ke pengaju untuk direvisi. Tuliskan catatan revisi yang jelas agar pengaju mengetahui apa yang perlu diperbaiki.') }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('Catatan Revisi') }} <span class="text-danger-500">*</span>
                            </label>
                            <textarea x-model="reviseComment" rows="3"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                                placeholder="{{ __('Tuliskan catatan revisi, misalnya: Mohon lampirkan surat penawaran vendor...') }}"></textarea>
                        </div>

                        <button type="button"
                            @click="reviseComment.trim() ? showConfirmModal = true : showAlert('{{ __('Catatan revisi wajib diisi') }}')"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-xl transition-colors">
                            <x-icon name="pencil-square" class="w-5 h-5" />
                            {{ __('Minta Revisi') }}
                        </button>
                    </div>

                    <!-- Confirmation Modal -->
                    <div x-show="showConfirmModal" x-transition
                        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/70 p-4 backdrop-blur-sm"
                        @click.self="showConfirmModal = false" style="display: none;">
                        <div class="w-full max-w-md rounded-3xl border border-gray-200/80 bg-white p-6 shadow-soft-lg dark:border-gray-700 dark:bg-gray-900" @click.stop>
                            <!-- Approve Confirmation -->
                            <template x-if="action === 'approve'">
                                <div>
                                    <div
                                        class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-success-100 dark:bg-success-900/30 rounded-full">
                                        <x-icon name="check-circle" class="w-10 h-10 text-success-600" />
                                    </div>
                                    <h3 class="text-xl font-semibold text-center text-gray-900 dark:text-white mb-2">
                                        {{ __('Konfirmasi Approval') }}
                                    </h3>
                                    <p class="text-center text-gray-600 dark:text-gray-400 mb-6">
                                        {{ __('Anda yakin ingin') }} <strong class="text-success-600">{{ __('MENYETUJUI') }}</strong> {{ __('dokumen ini?') }}
                                        <br><span class="text-sm">{{ __('Tindakan ini tidak dapat dibatalkan.') }}</span>
                                    </p>

                                    <form action="{{ route('approvals.approve', $document) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="approved_amount" :value="approvedAmount">
                                        <input type="hidden" name="comment" :value="approveComment">

                                        <div class="flex gap-3">
                                            <button type="button" @click="showConfirmModal = false"
                                                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-primary-50/60 dark:hover:bg-gray-700 transition-colors">
                                                {{ __('Batal') }}
                                            </button>
                                            <button type="submit" data-loading-text="{{ __('Menyetujui...') }}"
                                                class="flex-1 px-4 py-2 bg-success-600 hover:bg-success-700 text-white rounded-xl transition-colors">
                                                {{ __('Ya, Setujui') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </template>

                            <!-- Reject Confirmation -->
                            <template x-if="action === 'reject'">
                                <div>
                                    <div
                                        class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-danger-100 dark:bg-danger-900/30 rounded-full">
                                        <x-icon name="x-circle" class="w-10 h-10 text-danger-600" />
                                    </div>
                                    <h3 class="text-xl font-semibold text-center text-gray-900 dark:text-white mb-2">
                                        {{ __('Konfirmasi Penolakan') }}
                                    </h3>
                                    <p class="text-center text-gray-600 dark:text-gray-400 mb-6">
                                        {{ __('Anda yakin ingin') }} <strong class="text-danger-600">{{ __('MENOLAK') }}</strong> {{ __('dokumen ini?') }}
                                        <br><span class="text-sm">{{ __('Tindakan ini tidak dapat dibatalkan.') }}</span>
                                    </p>

                                    <form action="{{ route('approvals.reject', $document) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="comment" :value="rejectComment">

                                        <div class="flex gap-3">
                                            <button type="button" @click="showConfirmModal = false"
                                                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-primary-50/60 dark:hover:bg-gray-700 transition-colors">
                                                {{ __('Batal') }}
                                            </button>
                                            <button type="submit" data-loading-text="{{ __('Menolak...') }}"
                                                class="flex-1 px-4 py-2 bg-danger-600 hover:bg-danger-700 text-white rounded-xl transition-colors">
                                                {{ __('Ya, Tolak') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </template>

                            <!-- Revise Confirmation -->
                            <template x-if="action === 'revise'">
                                <div>
                                    <div
                                        class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-amber-100 dark:bg-amber-900/30 rounded-full">
                                        <x-icon name="pencil-square" class="w-10 h-10 text-amber-600" />
                                    </div>
                                    <h3 class="text-xl font-semibold text-center text-gray-900 dark:text-white mb-2">
                                        {{ __('Konfirmasi Permintaan Revisi') }}
                                    </h3>
                                    <p class="text-center text-gray-600 dark:text-gray-400 mb-6">
                                        {{ __('Anda yakin ingin') }} <strong class="text-amber-600">{{ __('MEMINTA REVISI') }}</strong> {{ __('dokumen ini?') }}
                                        <br><span class="text-sm">{{ __('Dokumen akan dikembalikan ke pengaju untuk diperbaiki.') }}</span>
                                    </p>

                                    <form action="{{ route('approvals.revise', $document) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="comment" :value="reviseComment">

                                        <div class="flex gap-3">
                                            <button type="button" @click="showConfirmModal = false"
                                                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-primary-50/60 dark:hover:bg-gray-700 transition-colors">
                                                {{ __('Batal') }}
                                            </button>
                                            <button type="submit" data-loading-text="{{ __('Mengirim...') }}"
                                                class="flex-1 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl transition-colors">
                                                {{ __('Ya, Minta Revisi') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <!-- Activity Log -->
            <x-document.activity-log :document="$document" />

            <!-- Approval Progress -->
            <x-ui.card>
                <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Status Approval') }}</h2>

                <div class="space-y-3">
                    @foreach($document->approvals as $approval)
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                                            {{ $approval->status === 'approved' ? 'bg-success-100 dark:bg-success-900/30' : '' }}
                                            {{ $approval->status === 'rejected' ? 'bg-danger-100 dark:bg-danger-900/30' : '' }}
                                            {{ $approval->status === 'pending' ? 'bg-yellow-100 dark:bg-yellow-900/30' : '' }}
                                            {{ $approval->status === 'skipped' ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
                                @if($approval->status === 'approved')
                                    <x-icon name="check-circle" class="w-5 h-5 text-success-600" />
                                @elseif($approval->status === 'rejected')
                                    <x-icon name="x-circle" class="w-5 h-5 text-danger-600" />
                                @elseif($approval->status === 'pending')
                                    <x-icon name="clock" class="w-5 h-5 text-yellow-600" />
                                @else
                                    <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $approval->approver?->position ?? $approval->role_label }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $approval->status === 'pending' && $approval->approver_id === auth()->id() ? __('Menunggu Anda') : $approval->status_label }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
