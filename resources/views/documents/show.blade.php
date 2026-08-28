<x-app-layout>
    <x-slot name="title">{{ $document->document_number }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ url()->previous() }}"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                </a>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                            {{ $document->document_number }}
                        </h1>
                        <x-ui.badge :status="$document->status_color" size="lg">
                            {{ $document->status_label }}
                        </x-ui.badge>
                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $document->title }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @can('update', $document)
                    <x-ui.button href="{{ route('documents.edit', $document) }}" variant="secondary" icon="pencil"
                        size="sm">
                        {{ __('Edit') }}
                    </x-ui.button>
                @endcan

                @can('submit', $document)
                    <form action="{{ route('documents.submit', $document) }}" method="POST" class="inline">
                        @csrf
                        <x-ui.button type="submit" icon="paper-airplane" size="sm">
                            {{ __('Kirim untuk Approval') }}
                        </x-ui.button>
                    </form>
                @endcan

                @can('print', $document)
    <x-ui.button href="{{ route('documents.print', $document) }}" target="_blank" variant="success"
        icon="printer" size="sm"
        onclick="setTimeout(() => { let f = document.createElement('iframe'); f.style.display='none'; f.src='{{ route('documents.download', $document) }}'; document.body.appendChild(f); setTimeout(() => f.remove(), 10000); }, 500)">
        {{ __('Cetak PDF') }}
    </x-ui.button>
@endcan

                @can('delete', $document)
                    <x-ui.button type="button" variant="danger" icon="trash" size="sm" x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-document-deletion')">
                        {{ __('Hapus') }}
                    </x-ui.button>
                @endcan

                {{-- Generate PO Button - Only for users with create-po permission, approved PO-type documents without existing PO --}}
                @if(auth()->user()->hasPermissionTo('create-po') && $document->status === 'approved' && $document->approval_type === 'po' && !$document->purchaseOrder)
                    <x-ui.button href="{{ route('finance.po.create', ['document_id' => $document->id]) }}" variant="primary"
                        icon="document-plus" size="sm">
                        {{ __('Generate PO') }}
                    </x-ui.button>
                @endif

                {{-- Generate Invoice Button - Only for users with create-invoice permission, approved Invoice-type documents without existing Invoice --}}
                @if(auth()->user()->hasPermissionTo('create-invoice') && $document->status === 'approved' && $document->approval_type === 'invoice' && !$document->invoice)
                    <x-ui.button href="{{ route('finance.invoices.create', ['document_id' => $document->id]) }}"
                        variant="primary" icon="document-plus" size="sm">
                        {{ __('Generate Invoice') }}
                    </x-ui.button>
                @endif

                {{-- Show link to existing PO --}}
                @if(auth()->user()->hasPermissionTo('view-finance-documents') && $document->purchaseOrder)
                    <x-ui.button href="{{ route('finance.po.show', $document->purchaseOrder) }}" variant="secondary"
                        icon="document-text" size="sm">
                        {{ __('Lihat PO') }}
                    </x-ui.button>
                @endif

                {{-- Show link to existing Invoice --}}
                @if(auth()->user()->hasPermissionTo('view-finance-documents') && $document->invoice)
                    <x-ui.button href="{{ route('finance.invoices.show', $document->invoice) }}" variant="secondary"
                        icon="document-text" size="sm">
                        {{ __('Lihat Invoice') }}
                    </x-ui.button>
                @endif
            </div>
        </div>
    </x-slot>

    {{-- Revision Requested Banner --}}
    @if($document->status === 'revision')
        <div class="mb-7 rounded-3xl border border-danger-200 bg-danger-50/80 p-6 shadow-soft">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-danger-100 flex items-center justify-center">
                    <x-icon name="pencil-square" class="w-6 h-6 text-danger-600" />
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-danger-900 mb-1">{{ __('Dokumen Perlu Direvisi') }}</h3>
                    <p class="text-sm text-danger-800 mb-4">
                        {{ __('Dokumen ini telah dikembalikan oleh approver untuk diperbaiki. Silakan edit dokumen ini dan klik "Kirim untuk Approval" untuk mensubmit ulang (proses approval akan mengulang dari awal).') }}
                    </p>

                    @if($document->revision_notes)
                        <div class="bg-white rounded-xl p-4 border border-danger-200 mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <x-icon name="chat-bubble-left-ellipsis" class="w-4 h-4 text-gray-500" />
                                <p class="text-xs text-gray-500 font-medium">{{ __('Catatan Revisi dari') }} {{ $document->revisionRequester->name ?? __('Approver') }}</p>
                            </div>
                            <p class="text-sm text-gray-800 font-medium whitespace-pre-wrap">{{ $document->revision_notes }}</p>
                            @if($document->revision_requested_at)
                                <p class="text-xs text-gray-400 mt-2">{{ $document->revision_requested_at->format('d F Y, H:i') }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Requester Amount Confirmation Banner --}}
    @if($document->status === 'pending_requester_confirmation' && (int) $document->user_id === (int) auth()->id())
        <div class="mb-7 rounded-3xl border border-amber-200 bg-amber-50/80 p-6 shadow-soft">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center">
                    <x-icon name="exclamation-triangle" class="w-6 h-6 text-amber-600" />
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-amber-900 mb-1">{{ __('Konfirmasi Perubahan Nominal') }}</h3>
                    <p class="text-sm text-amber-800 mb-4">
                        {{ __('Nominal pengajuan Anda telah diubah oleh approver. Silakan konfirmasi apakah Anda menyetujui nominal baru ini atau ingin membatalkan pengajuan.') }}
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div class="bg-white rounded-xl p-4 border border-amber-200">
                            <p class="text-xs text-gray-500 font-medium mb-1">{{ __('Nominal Pengajuan Awal') }}</p>
                            <p class="text-lg font-bold text-gray-400 line-through">
                                Rp {{ number_format($document->original_budget ?? $document->estimated_budget, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="bg-white rounded-xl p-4 border-2 border-amber-400">
                            <p class="text-xs text-amber-600 font-medium mb-1">{{ __('Nominal Disetujui') }}</p>
                            <p class="text-lg font-bold text-amber-700">
                                Rp {{ number_format($document->confirmed_amount, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ __('Selisih') }}: Rp {{ number_format(abs(($document->original_budget ?? $document->estimated_budget) - $document->confirmed_amount), 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <form action="{{ route('documents.confirm-amount', $document) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-success-600 px-5 py-2.5 text-sm font-bold text-white transition-all hover:bg-success-700 hover:shadow-md">
                                <x-icon name="check-circle" class="w-5 h-5" />
                                {{ __('Setuju dengan Nominal Baru') }}
                            </button>
                        </form>
                        <form action="{{ route('documents.reject-amount', $document) }}" method="POST" class="flex-1"
                              onsubmit="confirmAction(event, '{{ __('Apakah Anda yakin ingin menolak? Dokumen ini akan dibatalkan.') }}')">
                            @csrf
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-danger-600 px-5 py-2.5 text-sm font-bold text-white transition-all hover:bg-danger-700 hover:shadow-md">
                                <x-icon name="x-circle" class="w-5 h-5" />
                                {{ __('Tolak & Batalkan Pengajuan') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Document Info -->
            <x-ui.card>
                <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Informasi Dokumen') }}</h2>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Nomor Dokumen') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white font-medium">
                            {{ $document->document_number }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Jenis Approval') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $document->approval_type_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Prioritas') }}</dt>
                        <dd class="mt-1">
                            <x-ui.badge :status="$document->priority_color">{{ $document->priority_label }}</x-ui.badge>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Departemen') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $document->department ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Pengaju') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $document->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Tanggal Dibuat') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $document->created_at->format('d F Y H:i') }}
                        </dd>
                    </div>
                    @if($document->estimated_budget)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Estimasi Anggaran') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">Rp
                                {{ number_format($document->estimated_budget, 0, ',', '.') }}
                            </dd>
                        </div>
                    @endif
                    @if($document->approved_budget)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Anggaran Disetujui') }}</dt>
                            <dd class="mt-1 text-sm text-success-600 dark:text-success-400 font-semibold">Rp
                                {{ number_format($document->approved_budget, 0, ',', '.') }}
                            </dd>
                            @if($document->estimated_budget && (float) $document->approved_budget !== (float) $document->estimated_budget)
                                <dd class="text-xs text-yellow-600 dark:text-yellow-400 mt-0.5">
                                    📝 {{ __('Nominal diubah dari') }} Rp {{ number_format($document->estimated_budget, 0, ',', '.') }}
                                </dd>
                            @endif
                        </div>
                    @endif
                </dl>
            </x-ui.card>

            <!-- Description -->
            <x-ui.card>
                <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Deskripsi') }}</h2>
                <div class="prose prose-sm dark:prose-invert max-w-none">
                    {!! nl2br(e($document->description)) !!}
                </div>
            </x-ui.card>

            <x-document.type-details :document="$document" />
            <x-document.dynamic-fields :document="$document" />
            {{-- Approval Block (Signature Table) - Show for approved/completed documents --}}
            @if(in_array($document->status, ['approved', 'completed', 'paid', 'presiden_review', 'director_review', 'finance_review']))
                <x-ui.card class="print:shadow-none print:border-none">
                    <x-document.approval-block :type="$document->approval_type" :document="$document"
                        :approvals="$document->approvals" />

                    @if(in_array($document->approval_type, ['po', 'invoice', 'loan']))
                        <x-document.terms-conditions :type="$document->approval_type" />
                    @endif

                    <x-document.footer />
                </x-ui.card>
            @endif


            <!-- Attachments -->
            @if($document->attachments->count() > 0)
                <x-ui.card>
                    <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Lampiran') }}
                        ({{ $document->attachments->count() }})</h2>
                    <div class="space-y-2">
                        @foreach($document->attachments as $attachment)
                            <a href="{{ $attachment->url }}" target="_blank"
                                class="flex items-center justify-between p-3 bg-gray-50/80 dark:bg-gray-800 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center gap-3">
                                    <x-icon name="document-text" class="w-5 h-5 text-gray-400" />
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $attachment->original_filename }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $attachment->file_size_human }}</p>
                                    </div>
                                </div>
                                <x-icon name="arrow-down-tray" class="w-5 h-5 text-gray-400" />
                            </a>
                        @endforeach
                    </div>
                </x-ui.card>
            @endif

            <!-- Comments -->
            <x-ui.card>
                <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Komentar & Catatan') }}</h2>

                <div class="space-y-4 mb-6">
                    @forelse($document->comments as $comment)
                        <div class="flex items-start gap-3">
                            <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}"
                                class="w-8 h-8 rounded-full flex-shrink-0">
                            <div class="flex-1 rounded-2xl bg-gray-50/80 p-3 dark:bg-gray-800">
                                <div class="flex items-center justify-between mb-1">
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $comment->user->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $comment->comment }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">{{ __('Belum ada komentar') }}</p>
                    @endforelse
                </div>

                @auth
                    <form action="{{ route('documents.comment', $document) }}" method="POST">
                        @csrf
                        <div class="flex items-start gap-3">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                class="w-8 h-8 rounded-full flex-shrink-0">
                            <div class="flex-1">
                                <textarea name="comment" rows="2" required
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-4 focus:ring-primary-500/20/10"
                                    placeholder="{{ __('Tambahkan komentar...') }}"></textarea>
                                <div class="mt-2 flex justify-end">
                                    <x-ui.button type="submit" size="sm">{{ __('Kirim') }}</x-ui.button>
                                </div>
                            </div>
                        </div>
                    </form>
                @endauth
            </x-ui.card>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Approval Timeline -->
            <x-ui.card>
                <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Status Approval') }}</h2>

                <div class="relative">
                    @foreach($document->approvals as $index => $approval)
                        <div
                            class="flex gap-4 pb-6 {{ !$loop->last ? 'border-l-2 border-gray-200/80 dark:border-gray-700/80 ml-3' : '' }}">
                            <div class="flex-shrink-0 -ml-3 w-6 h-6 rounded-full flex items-center justify-center
                                                            {{ $approval->status === 'approved' ? 'bg-success-500' : '' }}
                                                            {{ $approval->status === 'rejected' ? 'bg-danger-500' : '' }}
                                                            {{ $approval->status === 'pending' ? 'bg-yellow-500' : '' }}
                                                            {{ $approval->status === 'skipped' ? 'bg-gray-400' : '' }}">
                                @if($approval->status === 'approved')
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @elseif($approval->status === 'rejected')
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                @elseif($approval->status === 'pending')
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4">
                                        </path>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $approval->role_label }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $approval->approver->name }}</p>
                                @if($approval->action_at)
                                    <p class="text-xs text-gray-400 mt-1">{{ $approval->action_at->format('d M Y H:i') }}</p>
                                @endif
                                @if($approval->comment)
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 italic">"{{ $approval->comment }}"
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>

            <x-document.activity-log :document="$document" />
        </div>
    </div>
    <x-modal name="confirm-document-deletion" focusable>
        <form method="post" action="{{ route('documents.destroy', $document) }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Apakah Anda yakin ingin menghapus dokumen ini?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Dokumen yang dihapus tidak dapat dikembalikan.') }}
            </p>

            <div class="mt-6 flex flex-wrap justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-danger-button class="ml-3">
                    {{ __('Hapus Dokumen') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
