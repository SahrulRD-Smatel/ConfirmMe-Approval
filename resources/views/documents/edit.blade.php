<x-app-layout>
    <x-slot name="title">Edit Dokumen</x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('documents.show', $document) }}"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Edit Dokumen</h1>
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
        class="space-y-7" x-data="{ 
              approvalType: '{{ old('approval_type', $document->approval_type) }}',
              selectedTemplate: {
                  code: '{{ $document->template }}',
                  dynamicFields: {{ Js::from($templates->where('code', $document->template)->first()?->fields['dynamic_fields'] ?? []) }}
              },
              metadata: {{ Js::from($metadata ?: new stdClass) }}
          }">
        @csrf
        @method('PUT')

        @php
            $currentTemplate = $templates->where('code', $document->template)->first();
            $templateName = $currentTemplate ? $currentTemplate->name : match($document->approval_type) {
                'only' => 'Administratif',
                'po' => 'Purchase Order',
                'invoice' => 'Invoice',
                'loan' => 'Employee Loan',
                default => 'Unknown'
            };
        @endphp

        <!-- Selected Module Indicator (Read-Only) -->
        <div class="flex items-center justify-between rounded-3xl border border-gray-200/80 bg-gray-50/80 p-5 shadow-soft dark:border-gray-700/80 dark:bg-gray-900/70">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400">
                    <x-icon name="lock-closed" class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider mb-1">Modul Terpilih (Terkunci)</p>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $templateName }}</h3>
                </div>
            </div>
            <div class="text-right hidden sm:block">
                <p class="text-xs text-gray-500 dark:text-gray-400">Jenis Approval</p>
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase">{{ $document->approval_type }}</p>
            </div>
        </div>

        <!-- Hidden Inputs to preserve the locked values -->
        <input type="hidden" name="approval_type" value="{{ $document->approval_type }}">
        <input type="hidden" name="template" value="{{ $document->template }}">

        <!-- Document Info -->
        <x-ui.card>
            <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Informasi Dokumen</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Judul Dokumen <span class="text-danger-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title', $document->title) }}" required
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10 focus:border-transparent"
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
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
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
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10 focus:border-transparent"
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
                <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Detail Dokumen Administratif</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="document_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Kategori Dokumen
                        </label>
                        <input type="text" name="document_category" id="document_category" readonly :value="selectedTemplate ? selectedTemplate.code : ''"
                            class="block w-full px-3 py-2 border border-gray-200/80 dark:border-gray-700/80 rounded-xl bg-gray-50/80 dark:bg-gray-800 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                    </div>
                    <div>
                        <label for="effective_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Tanggal Berlaku
                        </label>
                        <input type="date" name="effective_date" id="effective_date" value="{{ old('effective_date', $metadata['effective_date'] ?? '') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
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
                <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Detail Purchase Order</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="vendor_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Vendor <span class="text-danger-500">*</span>
                        </label>
                        <input type="text" name="vendor_name" id="vendor_name" value="{{ old('vendor_name', $metadata['vendor_name'] ?? '') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10"
                            placeholder="PT Supplier Indonesia">
                    </div>
                    <div>
                        <label for="po_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Kategori Pembelian <span class="text-danger-500">*</span>
                        </label>
                        <select name="po_category" id="po_category" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                            <option value="internal" {{ old('po_category', $document->po_category) === 'internal' ? 'selected' : '' }}>Internal Use</option>
                            <option value="external" {{ old('po_category', $document->po_category) === 'external' ? 'selected' : '' }}>External / Client</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="item_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Rincian Barang/Jasa <span class="text-danger-500">*</span>
                        </label>
                        <textarea name="item_description" id="item_description" rows="3"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10"
                            placeholder="1. Laptop x 5 unit @ Rp 15.000.000">{{ old('item_description', $metadata['item_description'] ?? '') }}</textarea>
                    </div>
                    <div class="md:col-span-2 bg-gray-50/80 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="subtotal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Subtotal (DPP) <span class="text-danger-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                                    <input type="text" name="subtotal" id="subtotal" value="{{ old('subtotal', $metadata['subtotal'] ?? 0) }}"
                                        class="currency-input block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10"
                                        inputmode="numeric" @currency-change="subtotal = $event.detail.value; calculateTax()">
                                </div>
                            </div>
                            <div>
                                <label for="tax_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    PPN 11% <span class="text-xs text-gray-400">- Auto</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                                    <input type="text" :value="formatNumber(taxAmount)" readonly
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-200/80 dark:border-gray-700/80 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 cursor-not-allowed">
                                    <input type="hidden" name="tax_amount" :value="taxAmount">
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-between items-center text-lg font-bold text-primary-700">
                            <span>Grand Total (Termasuk PPN)</span>
                            <span x-text="'Rp ' + formatNumber(grandTotal)"></span>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- DYNAMIC FIELDS -->
        <div x-show="selectedTemplate && selectedTemplate.dynamicFields && selectedTemplate.dynamicFields.length > 0" class="rounded-2xl border border-gray-100 bg-gray-50/60 p-4 dark:border-gray-800 dark:bg-gray-800/40 sm:p-5" x-cloak>
            <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">{{ __('Informasi Spesifik') }}</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <template x-for="field in (selectedTemplate?.dynamicFields || [])" :key="field.name">
                    <div :class="['textarea', 'table'].includes(field.type) ? 'md:col-span-2' : ''">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <span x-text="field.label"></span>
                            <span x-show="field.required == '1' || field.required === true" class="text-danger-500">*</span>
                        </label>
                        
                        <!-- Text / Number / Date / Time -->
                        <template x-if="['text', 'number', 'date', 'time'].includes(field.type)">
                            <input :type="field.type" :name="`metadata[${field.name}]`" x-model="metadata[field.name]" :required="field.required == '1' || field.required === true"
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20">
                        </template>

                        <!-- Textarea -->
                        <template x-if="field.type === 'textarea'">
                            <textarea :name="`metadata[${field.name}]`" x-model="metadata[field.name]" rows="3" :required="field.required == '1' || field.required === true"
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20"></textarea>
                        </template>

                        <!-- Select -->
                        <template x-if="field.type === 'select'">
                            <select :name="`metadata[${field.name}]`" x-model="metadata[field.name]" :required="field.required == '1' || field.required === true"
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20">
                                <option value="">{{ __('-- Pilih --') }}</option>
                                <template x-for="opt in (field.options ? (typeof field.options === 'string' ? field.options.split(/[\n,]+/) : field.options).map(s=>typeof s==='string'?s.trim():s).filter(s=>s) : [])">
                                    <option :value="opt" x-text="opt"></option>
                                </template>
                            </select>
                        </template>

                        <!-- File -->
                        <template x-if="field.type === 'file'">
                            <div>
                                <!-- Show existing file link if present -->
                                <template x-if="metadata[field.name]">
                                    <div class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('File saat ini:') }} 
                                        <a :href="`/storage/${metadata[field.name]}`" target="_blank" class="text-primary-600 hover:underline">Lihat Lampiran</a>
                                    </div>
                                </template>
                                <!-- File input for replacement -->
                                <input type="file" :name="`metadata_files[${field.name}]`" 
                                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                <p class="mt-1 text-xs text-gray-500">{{ __('Biarkan kosong jika tidak ingin mengubah file.') }}</p>
                            </div>
                        </template>

                        <!-- Table -->
                        <template x-if="field.type === 'table'">
                            <div class="bg-gray-50/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden"
                                 x-data="{
                                    rows: Array.isArray(metadata[field.name]) && metadata[field.name].length > 0 ? metadata[field.name] : [{}],
                                    addRow() { this.rows.push({}); },
                                    removeRow(rIndex) { if(this.rows.length > 1) this.rows.splice(rIndex, 1); },
                                    getCols(colsStr) { return colsStr ? (typeof colsStr === 'string' ? colsStr.split(',') : colsStr).map(s=>typeof s==='string'?s.trim():s) : []; }
                                 }">
                                <div class="overflow-x-auto">
                                    <table class="min-w-[680px] w-full text-left text-sm">
                                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                            <tr>
                                                <template x-for="col in getCols(field.columns)">
                                                    <th class="px-4 py-3" x-text="col"></th>
                                                </template>
                                                <th class="px-4 py-3 w-16 text-center">{{ __('Aksi') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(row, rIndex) in rows">
                                                <tr class="border-b dark:border-gray-700">
                                                    <template x-for="col in getCols(field.columns)">
                                                        <td class="p-2">
                                                            <input type="text" :name="`metadata[${field.name}][${rIndex}][${col}]`" x-model="row[col]" :placeholder="col" :required="field.required == '1' || field.required === true"
                                                                class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                        </td>
                                                    </template>
                                                    <td class="p-2 text-center">
                                                        <button type="button" @click="removeRow(rIndex)" class="text-danger-500 hover:text-danger-700" title="{{ __('Hapus Baris') }}">
                                                            <x-icon name="trash" class="w-4 h-4 mx-auto" />
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                    <button type="button" @click="addRow" class="text-xs font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1">
                                        <x-icon name="plus" class="w-3 h-3" /> {{ __('Tambah Baris') }}
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        <!-- Existing Attachments -->
        @if($document->attachments->count() > 0)
            <x-ui.card>
                <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Lampiran Saat Ini</h2>
                <div class="space-y-2">
                    @foreach($document->attachments as $attachment)
                        <div class="flex items-center justify-between p-3 bg-gray-50/80 dark:bg-gray-800 rounded-xl">
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
            <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Tambah Lampiran Baru</h2>

            <div
                class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:border-primary-400 transition-colors">
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
        <div class="flex flex-wrap items-center justify-end gap-3">
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
                        div.className = 'flex items-center justify-between rounded-xl bg-gray-50/80 p-3 dark:bg-gray-800';
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