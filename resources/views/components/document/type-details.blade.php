@props(['document'])
            <!-- Type-Specific Details -->
            @if($document->metadata && count($document->metadata) > 0)
                <x-ui.card>
                    @php $meta = $document->metadata; @endphp

                    @if($document->approval_type === 'only')
                        <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Detail Dokumen Administratif') }}</h2>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if(!empty($meta['document_category']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Kategori') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white capitalize">
                                        {{ str_replace('_', ' ', $meta['document_category']) }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['effective_date']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Tanggal Berlaku') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($meta['effective_date'])->format('d F Y') }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['expiry_date']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Tanggal Kadaluarsa') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($meta['expiry_date'])->format('d F Y') }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['related_department']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Departemen Terkait') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $meta['related_department'] }}</dd>
                                </div>
                            @endif
                        </dl>

                    @elseif($document->approval_type === 'po')
                        <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Detail Purchase Order') }}</h2>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if(!empty($meta['vendor_name']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Nama Vendor') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-medium">{{ $meta['vendor_name'] }}
                                    </dd>
                                </div>
                            @endif
                            @if(!empty($meta['vendor_npwp']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('NPWP Vendor') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $meta['vendor_npwp'] }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['budget_code']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Kode Anggaran') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $meta['budget_code'] }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['expected_delivery']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Estimasi Pengiriman') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($meta['expected_delivery'])->format('d F Y') }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['subtotal']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Subtotal') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">Rp
                                        {{ number_format($meta['subtotal'], 0, ',', '.') }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['tax_amount']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('PPN') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">Rp
                                        {{ number_format($meta['tax_amount'], 0, ',', '.') }}</dd>
                                </div>
                            @endif
                        </dl>
                        @if(!empty($meta['item_description']))
                            <div class="mt-4 pt-4 border-t border-gray-200/80 dark:border-gray-700/80">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('Rincian Barang/Jasa') }}</dt>
                                <dd
                                    class="text-sm text-gray-900 dark:text-white whitespace-pre-line bg-gray-50/80 dark:bg-gray-800 p-3 rounded-xl">
                                    {{ $meta['item_description'] }}</dd>
                            </div>
                        @endif

                    @elseif($document->approval_type === 'invoice')
                        <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Detail Invoice') }}</h2>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if(!empty($meta['invoice_number']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Nomor Invoice') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-medium">{{ $meta['invoice_number'] }}
                                    </dd>
                                </div>
                            @endif
                            @if(!empty($meta['invoice_date']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Tanggal Invoice') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($meta['invoice_date'])->format('d F Y') }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['vendor_name']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Nama Vendor') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $meta['vendor_name'] }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['related_po_number']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Nomor PO Terkait') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $meta['related_po_number'] }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['payment_due_date']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Jatuh Tempo') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($meta['payment_due_date'])->format('d F Y') }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['bank_account']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Rekening Tujuan') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $meta['bank_account'] }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['tax_invoice']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Nomor Faktur Pajak') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $meta['tax_invoice'] }}</dd>
                                </div>
                            @endif
                        </dl>

                    @elseif($document->approval_type === 'loan')
                        <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Detail Pinjaman Karyawan') }}</h2>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if(!empty($meta['loan_type']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Jenis Pinjaman') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white capitalize">Pinjaman
                                        {{ $meta['loan_type'] }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['loan_amount']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Jumlah Pinjaman') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-semibold">Rp
                                        {{ number_format($meta['loan_amount'], 0, ',', '.') }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['installment_count']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Jumlah Cicilan') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $meta['installment_count'] }} {{ __('Bulan') }}
                                    </dd>
                                </div>
                            @endif
                            @if(!empty($meta['repayment_start']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Mulai Potong Gaji') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $meta['repayment_start'] }}</dd>
                                </div>
                            @endif
                            @if(!empty($meta['loan_amount']) && !empty($meta['installment_count']))
                                @php
                                    $rawMonthly = $meta['loan_amount'] / $meta['installment_count'];
                                    $monthlyInstallment = ceil($rawMonthly / 1000) * 1000;
                                @endphp
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Cicilan per Bulan') }}</dt>
                                    <dd class="mt-1 text-sm text-success-600 dark:text-success-400 font-semibold">
                                        Rp {{ number_format($monthlyInstallment, 0, ',', '.') }}
                                    </dd>
                                </div>
                            @endif
                        </dl>
                        @if(!empty($meta['loan_reason']))
                            <div class="mt-4 pt-4 border-t border-gray-200/80 dark:border-gray-700/80">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('Alasan Pinjaman') }}</dt>
                                <dd
                                    class="text-sm text-gray-900 dark:text-white whitespace-pre-line bg-gray-50/80 dark:bg-gray-800 p-3 rounded-xl">
                                    {{ $meta['loan_reason'] }}</dd>
                            </div>
                        @endif
                    @endif
                </x-ui.card>
            @endif
