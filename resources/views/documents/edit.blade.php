<x-app-layout>
    <x-slot name="title">Edit Dokumen</x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('documents.show', $document) }}"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-display">Edit Dokumen</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $document->document_number }}
                </p>
            </div>
        </div>
    </x-slot>

    @php
        $metadata = $document->metadata ?? [];
    @endphp

    <form action="{{ route('documents.update', $document) }}" method="POST" enctype="multipart/form-data"
        class="space-y-6" x-data="{ 
              approvalType: '{{ old('approval_type', $document->approval_type) }}'
          }">
        @csrf
        @method('PUT')

        <!-- Approval Type Selection -->
        <x-ui.card>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Jenis Approval</h2>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Approval Only -->
                <label class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none transition-all"
                    :class="approvalType === 'only' ? 'border-primary-600 bg-primary-50 dark:bg-primary-900/20 ring-2 ring-primary-600' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'">
                    <input type="radio" name="approval_type" value="only" class="sr-only" x-model="approvalType" {{ old('approval_type', $document->approval_type) === 'only' ? 'checked' : '' }}>
                    <span class="flex flex-1 flex-col">
                        <span class="block text-sm font-medium text-gray-900 dark:text-white">Approval Only</span>
                        <span class="mt-1 text-xs text-gray-500">Non-finansial</span>
                    </span>
                </label>

                <!-- Approval to PO -->
                <label class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none transition-all"
                    :class="approvalType === 'po' ? 'border-primary-600 bg-primary-50 dark:bg-primary-900/20 ring-2 ring-primary-600' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'">
                    <input type="radio" name="approval_type" value="po" class="sr-only" x-model="approvalType" {{ old('approval_type', $document->approval_type) === 'po' ? 'checked' : '' }}>
                    <span class="flex flex-1 flex-col">
                        <span class="block text-sm font-medium text-gray-900 dark:text-white">Approval to PO</span>
                        <span class="mt-1 text-xs text-gray-500">Purchase Order</span>
                    </span>
                </label>

                <!-- Approval to Invoice -->
                <label class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none transition-all"
                    :class="approvalType === 'invoice' ? 'border-primary-600 bg-primary-50 dark:bg-primary-900/20 ring-2 ring-primary-600' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'">
                    <input type="radio" name="approval_type" value="invoice" class="sr-only" x-model="approvalType" {{ old('approval_type', $document->approval_type) === 'invoice' ? 'checked' : '' }}>
                    <span class="flex flex-1 flex-col">
                        <span class="block text-sm font-medium text-gray-900 dark:text-white">Approval to Invoice</span>
                        <span class="mt-1 text-xs text-gray-500">Pembayaran</span>
                    </span>
                </label>

                <!-- Employee Loan -->
                <label class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none transition-all"
                    :class="approvalType === 'loan' ? 'border-primary-600 bg-primary-50 dark:bg-primary-900/20 ring-2 ring-primary-600' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'">
                    <input type="radio" name="approval_type" value="loan" class="sr-only" x-model="approvalType" {{ old('approval_type', $document->approval_type) === 'loan' ? 'checked' : '' }}>
                    <span class="flex flex-1 flex-col">
                        <span class="block text-sm font-medium text-gray-900 dark:text-white">Employee Loan</span>
                        <span class="mt-1 text-xs text-gray-500">Potong Gaji</span>
                    </span>
                </label>
            </div>
            @error('approval_type')
                <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
            @enderror
        </x-ui.card>

        <!-- Document Info -->
        <x-ui.card>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Dokumen</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Judul Dokumen <span class="text-danger-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title', $document->title) }}" required
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        placeholder="Masukkan judul dokumen">
                    @error('title')
                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Prioritas <span class="text-danger-500">*</span>
                    </label>
                    <select name="priority" id="priority" required
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="low" {{ old('priority', $document->priority) === 'low' ? 'selected' : '' }}>Rendah
                        </option>
                        <option value="normal" {{ old('priority', $document->priority) === 'normal' ? 'selected' : '' }}>
                            Normal</option>
                        <option value="high" {{ old('priority', $document->priority) === 'high' ? 'selected' : '' }}>
                            Tinggi</option>
                        <option value="urgent" {{ old('priority', $document->priority) === 'urgent' ? 'selected' : '' }}>
                            Urgent</option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Deskripsi <span class="text-danger-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="5" required
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        placeholder="Jelaskan detail pengajuan Anda...">{{ old('description', $document->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-ui.card>

        <!-- Type-Specific Fields: APPROVAL ONLY -->
        <div x-show="approvalType === 'only'" x-transition>
            <x-ui.card>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Dokumen Administratif</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="document_category"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Kategori Dokumen
                        </label>
                        <select name="document_category" id="document_category"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="memo" {{ old('document_category', $metadata['document_category'] ?? '') === 'memo' ? 'selected' : '' }}>Memo Internal</option>
                            <option value="sop" {{ old('document_category', $metadata['document_category'] ?? '') === 'sop' ? 'selected' : '' }}>SOP / Kebijakan</option>
                            <option value="surat_tugas" {{ old('document_category', $metadata['document_category'] ?? '') === 'surat_tugas' ? 'selected' : '' }}>Surat Tugas</option>
                            <option value="surat_keputusan" {{ old('document_category', $metadata['document_category'] ?? '') === 'surat_keputusan' ? 'selected' : '' }}>Surat Keputusan</option>
                            <option value="lainnya" {{ old('document_category', $metadata['document_category'] ?? '') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label for="effective_date"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Tanggal Berlaku
                        </label>
                        <input type="date" name="effective_date" id="effective_date"
                            value="{{ old('effective_date', $metadata['effective_date'] ?? '') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label for="expiry_date"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Tanggal Kadaluarsa (Opsional)
                        </label>
                        <input type="date" name="expiry_date" id="expiry_date"
                            value="{{ old('expiry_date', $metadata['expiry_date'] ?? '') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label for="related_department"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Departemen Terkait
                        </label>
                        <input type="text" name="related_department" id="related_department"
                            value="{{ old('related_department', $metadata['related_department'] ?? '') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                            placeholder="IT, HR, Finance, dll">
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Type-Specific Fields: PO (Purchase Order) -->
        <div x-show="approvalType === 'po'" x-transition x-data="{
            subtotal: {{ old('subtotal', $metadata['subtotal'] ?? 0) }},
            taxAmount: {{ old('tax_amount', $metadata['tax_amount'] ?? 0) }},
            grandTotal: 0,
            calculateTax() {
                this.taxAmount = Math.round(this.subtotal * 0.11);
                this.grandTotal = parseInt(this.subtotal) + parseInt(this.taxAmount);
            },
            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            }
        }" x-init="calculateTax()">
            <x-ui.card>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Purchase Order</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="vendor_name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Vendor <span class="text-danger-500">*</span>
                        </label>
                        <input type="text" name="vendor_name" id="vendor_name"
                            value="{{ old('vendor_name', $metadata['vendor_name'] ?? '') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                            placeholder="PT Supplier Indonesia">
                    </div>
                    <div>
                        <label for="vendor_npwp"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            NPWP Vendor
                        </label>
                        <input type="text" name="vendor_npwp" id="vendor_npwp"
                            value="{{ old('vendor_npwp', $metadata['vendor_npwp'] ?? '') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                            placeholder="XX.XXX.XXX.X-XXX.XXX">
                    </div>
                    <div class="md:col-span-2">
                        <label for="item_description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Rincian Barang/Jasa <span class="text-danger-500">*</span>
                        </label>
                        <textarea name="item_description" id="item_description" rows="3"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                            placeholder="1. Laptop x 5 unit @ Rp 15.000.000">{{ old('item_description', $metadata['item_description'] ?? '') }}</textarea>
                    </div>
                    <div>
                        <label for="subtotal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Subtotal (Sebelum Pajak) <span class="text-danger-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                            <input type="text" name="subtotal" id="subtotal"
                                value="{{ old('subtotal', $metadata['subtotal'] ?? 0) }}"
                                class="currency-input block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                                inputmode="numeric"
                                @currency-change="subtotal = $event.detail.value; calculateTax()">
                        </div>
                    </div>
                    <div>
                        <label for="tax_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            PPN (11%) <span class="text-xs text-gray-400">- Auto</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                            <input type="text" :value="formatNumber(taxAmount)" readonly
                                class="block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 cursor-not-allowed">
                            <input type="hidden" name="tax_amount" :value="taxAmount">
                        </div>
                    </div>
                    <div class="md:col-span-2 p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Grand Total (Termasuk
                                PPN)</span>
                            <span class="text-xl font-bold text-primary-600 dark:text-primary-400"
                                x-text="'Rp ' + formatNumber(grandTotal)"></span>
                        </div>
                    </div>
                    <div>
                        <label for="budget_code"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Kode Anggaran
                        </label>
                        <input type="text" name="budget_code" id="budget_code"
                            value="{{ old('budget_code', $metadata['budget_code'] ?? '') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                            placeholder="IT-2026-001">
                    </div>
                    <div>
                        <label for="expected_delivery"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Estimasi Pengiriman
                        </label>
                        <input type="date" name="expected_delivery" id="expected_delivery"
                            value="{{ old('expected_delivery', $metadata['expected_delivery'] ?? '') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Type-Specific Fields: INVOICE -->
        <div x-show="approvalType === 'invoice'" x-transition>
            <x-ui.card>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Invoice / Tagihan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="invoice_number"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nomor Invoice <span class="text-danger-500">*</span>
                        </label>
                        <input type="text" name="invoice_number" id="invoice_number"
                            value="{{ old('invoice_number', $metadata['invoice_number'] ?? '') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                            placeholder="INV-2026-0001">
                    </div>
                    <div>
                        <label for="invoice_date"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Tanggal Invoice <span class="text-danger-500">*</span>
                        </label>
                        <input type="date" name="invoice_date" id="invoice_date"
                            value="{{ old('invoice_date', $metadata['invoice_date'] ?? '') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label for="related_po_number"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nomor PO Terkait
                        </label>
                        <input type="text" name="related_po_number" id="related_po_number"
                            value="{{ old('related_po_number', $metadata['related_po_number'] ?? '') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                            placeholder="PO-2026-0001">
                    </div>
                    <div>
                        <label for="vendor_name_invoice"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Vendor <span class="text-danger-500">*</span>
                        </label>
                        <input type="text" name="vendor_name_invoice" id="vendor_name_invoice"
                            value="{{ old('vendor_name_invoice', $metadata['vendor_name'] ?? '') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                            placeholder="PT Supplier Indonesia">
                    </div>
                    <div>
                        <label for="invoice_amount"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nilai Tagihan <span class="text-danger-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                            <input type="text" name="invoice_amount" id="invoice_amount"
                                value="{{ old('invoice_amount', $metadata['invoice_amount'] ?? '') }}"
                                class="currency-input block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                                inputmode="numeric"
                                placeholder="0">
                        </div>
                    </div>
                    <div>
                        <label for="payment_due_date"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Jatuh Tempo Pembayaran <span class="text-danger-500">*</span>
                        </label>
                        <input type="date" name="payment_due_date" id="payment_due_date"
                            value="{{ old('payment_due_date', $metadata['payment_due_date'] ?? '') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label for="bank_account"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Rekening Tujuan
                        </label>
                        <input type="text" name="bank_account" id="bank_account"
                            value="{{ old('bank_account', $metadata['bank_account'] ?? '') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                            placeholder="BCA - 1234567890 a.n. PT Supplier">
                    </div>
                    <div>
                        <label for="tax_invoice"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nomor Faktur Pajak
                        </label>
                        <input type="text" name="tax_invoice" id="tax_invoice"
                            value="{{ old('tax_invoice', $metadata['tax_invoice'] ?? '') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                            placeholder="010.000-26.00000001">
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Type-Specific Fields: LOAN -->
        <div x-show="approvalType === 'loan'" x-transition>
            <x-ui.card>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Pinjaman Karyawan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="loan_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Jenis Pinjaman <span class="text-danger-500">*</span>
                        </label>
                        <select name="loan_type" id="loan_type"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="darurat" {{ old('loan_type', $metadata['loan_type'] ?? '') === 'darurat' ? 'selected' : '' }}>Pinjaman Darurat</option>
                            <option value="pendidikan" {{ old('loan_type', $metadata['loan_type'] ?? '') === 'pendidikan' ? 'selected' : '' }}>Pinjaman Pendidikan</option>
                            <option value="perumahan" {{ old('loan_type', $metadata['loan_type'] ?? '') === 'perumahan' ? 'selected' : '' }}>Pinjaman Perumahan</option>
                            <option value="kendaraan" {{ old('loan_type', $metadata['loan_type'] ?? '') === 'kendaraan' ? 'selected' : '' }}>Pinjaman Kendaraan</option>
                            <option value="lainnya" {{ old('loan_type', $metadata['loan_type'] ?? '') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label for="loan_amount"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Jumlah Pinjaman <span class="text-danger-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                            <input type="text" name="loan_amount" id="loan_amount"
                                value="{{ old('loan_amount', $metadata['loan_amount'] ?? '') }}"
                                class="currency-input block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                                inputmode="numeric">
                        </div>
                    </div>
                    <div>
                        <label for="installment_count"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Jumlah Cicilan (Bulan) <span class="text-danger-500">*</span>
                        </label>
                        <select name="installment_count" id="installment_count"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="3" {{ old('installment_count', $metadata['installment_count'] ?? '') == '3' ? 'selected' : '' }}>3 Bulan</option>
                            <option value="6" {{ old('installment_count', $metadata['installment_count'] ?? '') == '6' ? 'selected' : '' }}>6 Bulan</option>
                            <option value="12" {{ old('installment_count', $metadata['installment_count'] ?? '') == '12' ? 'selected' : '' }}>12 Bulan</option>
                            <option value="24" {{ old('installment_count', $metadata['installment_count'] ?? '') == '24' ? 'selected' : '' }}>24 Bulan</option>
                        </select>
                    </div>
                    <div>
                        <label for="repayment_start"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Mulai Potong Gaji <span class="text-danger-500">*</span>
                        </label>
                        <input type="month" name="repayment_start" id="repayment_start"
                            value="{{ old('repayment_start', $metadata['repayment_start'] ?? '') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="loan_reason"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Alasan Pinjaman <span class="text-danger-500">*</span>
                        </label>
                        <textarea name="loan_reason" id="loan_reason" rows="3"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                            placeholder="Jelaskan alasan pengajuan pinjaman...">{{ old('loan_reason', $metadata['loan_reason'] ?? '') }}</textarea>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Existing Attachments -->
        @if($document->attachments->count() > 0)
            <x-ui.card>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Lampiran Saat Ini</h2>
                <div class="space-y-2">
                    @foreach($document->attachments as $attachment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center gap-3">
                                <x-icon name="document-text" class="w-5 h-5 text-gray-400" />
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $attachment->original_filename }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $attachment->file_size_human }}</p>
                                </div>
                            </div>
                            <a href="{{ $attachment->url }}" target="_blank"
                                class="text-primary-600 hover:text-primary-800 text-sm">
                                Lihat
                            </a>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        @endif

        <!-- New Attachments -->
        <x-ui.card>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tambah Lampiran Baru</h2>

            <div
                class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center hover:border-primary-400 transition-colors">
                <input type="file" name="attachments[]" id="attachments" multiple
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" class="hidden">
                <label for="attachments" class="cursor-pointer">
                    <x-icon name="arrow-down-tray" class="w-10 h-10 text-gray-400 mx-auto mb-3" />
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="text-primary-600 dark:text-primary-400 font-medium">Klik untuk upload</span> atau
                        drag & drop
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                        PDF, DOC, XLS, JPG, PNG (Max 10MB per file)
                    </p>
                </label>
            </div>
            <div id="file-list" class="mt-4 space-y-2"></div>
            @error('attachments.*')
                <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
            @enderror
        </x-ui.card>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4">
            <x-ui.button type="button" variant="secondary" href="{{ route('documents.show', $document) }}">
                Batal
            </x-ui.button>
            <x-ui.button type="submit" icon="check">
                Simpan Perubahan
            </x-ui.button>
        </div>
    </form>

    @push('scripts')
        <script>
            // Prevent double submission
            document.querySelector('form').addEventListener('submit', function (e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn.disabled) {
                    e.preventDefault();
                    return false;
                }
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan...';
            });

            // File upload preview
            const attachmentsInput = document.getElementById('attachments');
            if (attachmentsInput) {
                attachmentsInput.addEventListener('change', function (e) {
                    const fileList = document.getElementById('file-list');
                    if (!fileList) return;
                    fileList.innerHTML = '';

                    if (!e.target.files || e.target.files.length === 0) return;

                    Array.from(e.target.files).forEach(file => {
                        if (!file || !file.name) return;

                        const div = document.createElement('div');
                        div.className = 'flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg';
                        div.innerHTML = `
                                            <div class="flex items-center gap-3">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span class="text-sm text-gray-700 dark:text-gray-300">${file.name}</span>
                                            </div>
                                            <span class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
                                        `;
                        fileList.appendChild(div);
                    });
                });
            }

            // Approval type selection
            document.querySelectorAll('input[name="approval_type"]').forEach(radio => {
                radio.addEventListener('change', function () {
                    document.querySelectorAll('input[name="approval_type"]').forEach(r => {
                        r.closest('label').classList.remove('border-primary-600', 'bg-primary-50', 'dark:bg-primary-900/20', 'ring-2', 'ring-primary-600');
                        r.closest('label').classList.add('border-gray-200', 'dark:border-gray-700');
                    });
                    this.closest('label').classList.remove('border-gray-200', 'dark:border-gray-700');
                    this.closest('label').classList.add('border-primary-600', 'bg-primary-50', 'dark:bg-primary-900/20', 'ring-2', 'ring-primary-600');
                });
            });
        </script>
    @endpush
</x-app-layout>