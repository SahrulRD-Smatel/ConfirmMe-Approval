<x-app-layout>
    <x-slot name="title">{{ __('Arsip') }}: {{ $document->document_number }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('archive.index') }}"
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
                @can('print', $document)
                    <x-ui.button href="{{ route('documents.print', $document) }}" target="_blank" variant="success"
                        icon="printer" size="sm"
                        onclick="setTimeout(() => { let f = document.createElement('iframe'); f.style.display='none'; f.src='{{ route('documents.download', $document) }}'; document.body.appendChild(f); setTimeout(() => f.remove(), 10000); }, 500)">
                        {{ __('Cetak PDF') }}
                    </x-ui.button>
                @endcan
            </div>
        </div>
    </x-slot>

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
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Dibuat Oleh') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $document->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Tanggal Dibuat') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $document->created_at->format('d M Y H:i') }}
                        </dd>
                    </div>
                    @if($document->approved_budget)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Anggaran') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white font-medium">
                                Rp {{ number_format($document->approved_budget, 0, ',', '.') }}
                            </dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Status Akhir') }}</dt>
                        <dd class="mt-1">
                            <x-ui.badge :status="$document->status_color">{{ $document->status_label }}</x-ui.badge>
                        </dd>
                    </div>
                </dl>
            </x-ui.card>

            <!-- Content -->
            <x-ui.card>
                <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Isi Dokumen') }}</h2>
                <div class="prose prose-sm max-w-none dark:prose-invert">
                    {!! nl2br(e($document->content)) !!}
                </div>
            </x-ui.card>

            <!-- Attachments -->
            @if($document->attachments->count())
                <x-ui.card>
                    <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Lampiran') }}</h2>
                    <ul class="divide-y divide-gray-200/80 dark:divide-gray-700/80">
                        @foreach($document->attachments as $attachment)
                            <li class="py-3 flex items-center justify-between">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $attachment->original_name }}</span>
                                <a href="{{ Storage::url($attachment->file_path) }}" target="_blank"
                                    class="text-primary-600 hover:text-primary-800 text-sm">
                                    {{ __('Download') }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-ui.card>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Approval Timeline -->
            <x-ui.card>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">{{ __('Riwayat Approval') }}</h3>

                <div class="space-y-4">
                    @foreach($document->approvals as $approval)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                @if($approval->status === 'approved')
                                    <div
                                        class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                        <x-icon name="check" class="w-4 h-4 text-green-600" />
                                    </div>
                                @elseif($approval->status === 'rejected')
                                    <div
                                        class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                        <x-icon name="x-mark" class="w-4 h-4 text-red-600" />
                                    </div>
                                @else
                                    <div
                                        class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        <x-icon name="clock" class="w-4 h-4 text-gray-400" />
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $approval->approver->name ?? 'Unknown' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ config('workflow.role_labels')[$approval->role] ?? ucfirst($approval->role) }}
                                </p>
                                @if($approval->approved_at)
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $approval->approved_at->format('d M Y H:i') }}
                                    </p>
                                @endif
                                @if($approval->notes)
                                    <p class="text-xs text-gray-500 mt-1 italic">{{ $approval->notes }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>

            <!-- Related PO/Invoice -->
            @if($document->purchaseOrder)
                <x-ui.card>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Purchase Order</h3>
                    <a href="{{ route('finance.po.show', $document->purchaseOrder) }}"
                        class="block rounded-2xl border border-gray-200/80 bg-gray-50/80 p-4 transition hover:border-primary-200 hover:bg-primary-50/60 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
                        <p class="text-sm font-medium text-primary-600">{{ $document->purchaseOrder->po_number }}</p>
                        <p class="text-xs text-gray-500">{{ $document->purchaseOrder->vendor_name }}</p>
                    </a>
                </x-ui.card>
            @endif

            @if($document->invoice)
                <x-ui.card>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Invoice</h3>
                    <a href="{{ route('finance.invoices.show', $document->invoice) }}"
                        class="block rounded-2xl border border-gray-200/80 bg-gray-50/80 p-4 transition hover:border-primary-200 hover:bg-primary-50/60 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
                        <p class="text-sm font-medium text-primary-600">{{ $document->invoice->invoice_number }}</p>
                        <p class="text-xs text-gray-500">{{ $document->invoice->vendor_name }}</p>
                    </a>
                </x-ui.card>
            @endif
        </div>
    </div>
</x-app-layout>