<x-app-layout>
    <x-slot name="title">{{ $document->document_number }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ url()->previous() }}"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                </a>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-display">
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
                        Edit
                    </x-ui.button>
                @endcan

                @can('submit', $document)
                    <form action="{{ route('documents.submit', $document) }}" method="POST" class="inline">
                        @csrf
                        <x-ui.button type="submit" icon="paper-airplane" size="sm">
                            Kirim untuk Approval
                        </x-ui.button>
                    </form>
                @endcan

                @can('print', $document)
    <x-ui.button href="{{ route('documents.print', $document) }}" target="_blank" variant="success"
        icon="printer" size="sm"
        onclick="setTimeout(() => { let f = document.createElement('iframe'); f.style.display='none'; f.src='{{ route('documents.download', $document) }}'; document.body.appendChild(f); setTimeout(() => f.remove(), 10000); }, 500)">
        Cetak PDF
    </x-ui.button>
@endcan

                @can('delete', $document)
                    <x-ui.button type="button" variant="danger" icon="trash" size="sm" x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-document-deletion')">
                        Hapus
                    </x-ui.button>
                @endcan

                {{-- Generate PO Button - Only for Finance, approved PO-type documents without existing PO --}}
                @if(auth()->user()->hasRole('finance') && $document->status === 'approved' && $document->approval_type === 'po' && !$document->purchaseOrder)
                    <x-ui.button href="{{ route('finance.po.create', ['document_id' => $document->id]) }}" variant="primary"
                        icon="document-plus" size="sm">
                        Generate PO
                    </x-ui.button>
                @endif

                {{-- Generate Invoice Button - Only for Finance, approved Invoice-type documents without existing
                Invoice --}}
                @if(auth()->user()->hasRole('finance') && $document->status === 'approved' && $document->approval_type === 'invoice' && !$document->invoice)
                    <x-ui.button href="{{ route('finance.invoices.create', ['document_id' => $document->id]) }}"
                        variant="primary" icon="document-plus" size="sm">
                        Generate Invoice
                    </x-ui.button>
                @endif

                {{-- Show link to existing PO - Only for Finance --}}
                @if(auth()->user()->hasRole('finance') && $document->purchaseOrder)
                    <x-ui.button href="{{ route('finance.po.show', $document->purchaseOrder) }}" variant="secondary"
                        icon="document-text" size="sm">
                        Lihat PO
                    </x-ui.button>
                @endif

                {{-- Show link to existing Invoice - Only for Finance --}}
                @if(auth()->user()->hasRole('finance') && $document->invoice)
                    <x-ui.button href="{{ route('finance.invoices.show', $document->invoice) }}" variant="secondary"
                        icon="document-text" size="sm">
                        Lihat Invoice
                    </x-ui.button>
                @endif
            </div>
        </div>
    </x-slot>

    {{-- Requester Amount Confirmation Banner --}}
    @if($document->status === 'pending_requester_confirmation' && (int) $document->user_id === (int) auth()->id())
        <div class="mb-6 bg-amber-50 border-2 border-amber-300 rounded-2xl p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center">
                    <x-icon name="exclamation-triangle" class="w-6 h-6 text-amber-600" />
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-amber-900 mb-1">Konfirmasi Perubahan Nominal</h3>
                    <p class="text-sm text-amber-800 mb-4">
                        Nominal pengajuan Anda telah diubah oleh approver. Silakan konfirmasi apakah Anda menyetujui nominal baru ini atau ingin membatalkan pengajuan.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div class="bg-white rounded-xl p-4 border border-amber-200">
                            <p class="text-xs text-gray-500 font-medium mb-1">Nominal Pengajuan Awal</p>
                            <p class="text-lg font-bold text-gray-400 line-through">
                                Rp {{ number_format($document->original_budget ?? $document->estimated_budget, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="bg-white rounded-xl p-4 border-2 border-amber-400">
                            <p class="text-xs text-amber-600 font-medium mb-1">Nominal Disetujui</p>
                            <p class="text-lg font-bold text-amber-700">
                                Rp {{ number_format($document->confirmed_amount, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Selisih: Rp {{ number_format(abs(($document->original_budget ?? $document->estimated_budget) - $document->confirmed_amount), 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <form action="{{ route('documents.confirm-amount', $document) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold text-sm rounded-xl transition-all hover:shadow-lg hover:shadow-green-500/25">
                                <x-icon name="check-circle" class="w-5 h-5" />
                                Setuju dengan Nominal Baru
                            </button>
                        </form>
                        <form action="{{ route('documents.reject-amount', $document) }}" method="POST" class="flex-1"
                              onsubmit="confirmAction(event, 'Apakah Anda yakin ingin menolak? Dokumen ini akan dibatalkan.')">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-sm rounded-xl transition-all hover:shadow-lg hover:shadow-red-500/25">
                                <x-icon name="x-circle" class="w-5 h-5" />
                                Tolak & Batalkan Pengajuan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Document Info -->
            <x-ui.card>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Dokumen</h2>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nomor Dokumen</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white font-medium">
                            {{ $document->document_number }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jenis Approval</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $document->approval_type_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Prioritas</dt>
                        <dd class="mt-1">
                            <x-ui.badge :status="$document->priority_color">{{ $document->priority_label }}</x-ui.badge>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Departemen</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $document->department ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Pengaju</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $document->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Dibuat</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $document->created_at->format('d F Y H:i') }}
                        </dd>
                    </div>
                    @if($document->estimated_budget)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Estimasi Anggaran</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">Rp
                                {{ number_format($document->estimated_budget, 0, ',', '.') }}
                            </dd>
                        </div>
                    @endif
                    @if($document->approved_budget)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Anggaran Disetujui</dt>
                            <dd class="mt-1 text-sm text-success-600 dark:text-success-400 font-semibold">Rp
                                {{ number_format($document->approved_budget, 0, ',', '.') }}
                            </dd>
                            @if($document->estimated_budget && (float) $document->approved_budget !== (float) $document->estimated_budget)
                                <dd class="text-xs text-yellow-600 dark:text-yellow-400 mt-0.5">
                                    📝 Nominal diubah dari Rp {{ number_format($document->estimated_budget, 0, ',', '.') }}
                                </dd>
                            @endif
                        </div>
                    @endif
                </dl>
            </x-ui.card>

            <!-- Description -->
            <x-ui.card>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Deskripsi</h2>
                <div class="prose prose-sm dark:prose-invert max-w-none">
                    {!! nl2br(e($document->description)) !!}
                </div>
            </x-ui.card>

            <!-- Type-Specific Details -->
            @if($document->metadata && count($document->metadata) > 0)
                <x-ui.card>
                    @php $meta = $document->metadata; @endphp

                    @if($document->approval_type === 'only')
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Dokumen Administratif</h2>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if(!empty($meta['document_category']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kategori</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white capitalize">
                                        {{ str_replace('_', ' ', $meta['document_category']) }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['effective_date']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Berlaku</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($meta['effective_date'])->format('d F Y') }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['expiry_date']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Kadaluarsa</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($meta['expiry_date'])->format('d F Y') }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['related_department']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Departemen Terkait</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $meta['related_department'] }}</dd>
                                </div>
                            @endif
                        </dl>

                    @elseif($document->approval_type === 'po')
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Purchase Order</h2>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if(!empty($meta['vendor_name']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Vendor</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-medium">{{ $meta['vendor_name'] }}
                                    </dd>
                                </div>
                            @endif
                            @if(!empty($meta['vendor_npwp']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">NPWP Vendor</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $meta['vendor_npwp'] }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['budget_code']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kode Anggaran</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $meta['budget_code'] }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['expected_delivery']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Estimasi Pengiriman</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($meta['expected_delivery'])->format('d F Y') }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['subtotal']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Subtotal</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">Rp
                                        {{ number_format($meta['subtotal'], 0, ',', '.') }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['tax_amount']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">PPN</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">Rp
                                        {{ number_format($meta['tax_amount'], 0, ',', '.') }}</dd>
                                </div>
                            @endif
                        </dl>
                        @if(!empty($meta['item_description']))
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Rincian Barang/Jasa</dt>
                                <dd
                                    class="text-sm text-gray-900 dark:text-white whitespace-pre-line bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                                    {{ $meta['item_description'] }}</dd>
                            </div>
                        @endif

                    @elseif($document->approval_type === 'invoice')
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Invoice</h2>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if(!empty($meta['invoice_number']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nomor Invoice</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-medium">{{ $meta['invoice_number'] }}
                                    </dd>
                                </div>
                            @endif
                            @if(!empty($meta['invoice_date']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Invoice</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($meta['invoice_date'])->format('d F Y') }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['vendor_name']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Vendor</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $meta['vendor_name'] }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['related_po_number']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nomor PO Terkait</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $meta['related_po_number'] }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['payment_due_date']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jatuh Tempo</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($meta['payment_due_date'])->format('d F Y') }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['bank_account']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rekening Tujuan</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $meta['bank_account'] }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['tax_invoice']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nomor Faktur Pajak</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $meta['tax_invoice'] }}</dd>
                                </div>
                            @endif
                        </dl>

                    @elseif($document->approval_type === 'loan')
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Pinjaman Karyawan</h2>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if(!empty($meta['loan_type']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jenis Pinjaman</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white capitalize">Pinjaman
                                        {{ $meta['loan_type'] }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['loan_amount']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Pinjaman</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-semibold">Rp
                                        {{ number_format($meta['loan_amount'], 0, ',', '.') }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['installment_count']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Cicilan</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $meta['installment_count'] }} Bulan
                                    </dd>
                                </div>
                            @endif
                            @if(!empty($meta['repayment_start']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Mulai Potong Gaji</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $meta['repayment_start'] }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['loan_amount']) && !empty($meta['installment_count']))
                                @php
                                    $rawMonthly = $meta['loan_amount'] / $meta['installment_count'];
                                    $monthlyInstallment = ceil($rawMonthly / 1000) * 1000;
                                @endphp
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Cicilan per Bulan</dt>
                                    <dd class="mt-1 text-sm text-success-600 dark:text-success-400 font-semibold">
                                        Rp {{ number_format($monthlyInstallment, 0, ',', '.') }}
                                    </dd>
                                </div>
                            @endif
                        </dl>
                        @if(!empty($meta['loan_reason']))
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Alasan Pinjaman</dt>
                                <dd
                                    class="text-sm text-gray-900 dark:text-white whitespace-pre-line bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                                    {{ $meta['loan_reason'] }}</dd>
                            </div>
                        @endif
                    @endif
                </x-ui.card>
            @endif

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
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Lampiran
                        ({{ $document->attachments->count() }})</h2>
                    <div class="space-y-2">
                        @foreach($document->attachments as $attachment)
                            <a href="{{ $attachment->url }}" target="_blank"
                                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
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
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Komentar & Catatan</h2>

                <div class="space-y-4 mb-6">
                    @forelse($document->comments as $comment)
                        <div class="flex gap-3">
                            <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}"
                                class="w-8 h-8 rounded-full flex-shrink-0">
                            <div class="flex-1 bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                <div class="flex items-center justify-between mb-1">
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $comment->user->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $comment->comment }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada komentar</p>
                    @endforelse
                </div>

                @auth
                    <form action="{{ route('documents.comment', $document) }}" method="POST">
                        @csrf
                        <div class="flex gap-3">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                class="w-8 h-8 rounded-full flex-shrink-0">
                            <div class="flex-1">
                                <textarea name="comment" rows="2" required
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                                    placeholder="Tambahkan komentar..."></textarea>
                                <div class="mt-2 flex justify-end">
                                    <x-ui.button type="submit" size="sm">Kirim</x-ui.button>
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
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status Approval</h2>

                <div class="relative">
                    @foreach($document->approvals as $index => $approval)
                        <div
                            class="flex gap-4 pb-6 {{ !$loop->last ? 'border-l-2 border-gray-200 dark:border-gray-700 ml-3' : '' }}">
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

            <!-- Activity Log -->
            <x-ui.card>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Riwayat Aktivitas</h2>

                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse($document->approvalLogs->take(10) as $log)
                        <div class="flex items-start gap-3 text-sm">
                            <div
                                class="flex-shrink-0 w-8 h-8 rounded-full bg-{{ $log->action_color }}-100 dark:bg-{{ $log->action_color }}-900/30 flex items-center justify-center">
                                <x-icon :name="$log->action_icon"
                                    class="w-4 h-4 text-{{ $log->action_color }}-600 dark:text-{{ $log->action_color }}-400" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-gray-900 dark:text-white">
                                    <span class="font-medium">{{ $log->user->name ?? 'System' }}</span>
                                    {{ $log->action_label }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">Belum ada aktivitas</p>
                    @endforelse
                </div>
            </x-ui.card>
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

            <div class="mt-6 flex justify-end">
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