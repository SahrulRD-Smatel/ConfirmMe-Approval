<x-app-layout>
    <x-slot name="title">{{ isset($template) ? __('Edit Template') : __('Buat Template') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.templates.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    {{ isset($template) ? __('Edit Template') : __('Buat Template') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Isi default values agar user tidak perlu mengisi ulang setiap kali') }}</p>
            </div>
        </div>
    </x-slot>

    @php
        $fields = old('fields', $template->fields ?? []);
    @endphp

    <form action="{{ isset($template) ? route('admin.templates.update', $template) : route('admin.templates.store') }}"
        method="POST" class="mx-auto max-w-7xl space-y-6" x-data="{
            approvalType: '{{ old('approval_type', $template->approval_type ?? 'only') }}',
            workflows: {{ Js::from(old('workflow', isset($template) ? (array) $template->workflow : [])) }},
            init() {
                this.workflows = this.workflows.map(w => {
                    if (w && !w.startsWith('role:') && !w.startsWith('user:')) {
                        return 'role:' + w;
                    }
                    return w;
                });
            },
            addWorkflow() {
                this.workflows.push('');
            },
            removeWorkflow(index) {
                this.workflows.splice(index, 1);
            },
            moveWorkflowUp(index) {
                if (index > 0) {
                    const temp = this.workflows[index];
                    this.workflows.splice(index, 1);
                    this.workflows.splice(index - 1, 0, temp);
                }
            },
            moveWorkflowDown(index) {
                if (index < this.workflows.length - 1) {
                    const temp = this.workflows[index];
                    this.workflows.splice(index, 1);
                    this.workflows.splice(index + 1, 0, temp);
                }
            }
        }"
        @submit="if(workflows.length === 0) { window.showAlert('{{ __('Harap tambahkan minimal 1 approver pada Susunan Approver!') }}', 'warning'); $event.preventDefault(); }">
        @csrf
        @if(isset($template)) @method('PUT') @endif


        <div class="grid grid-cols-1 items-start gap-5 xl:grid-cols-[minmax(0,1fr)_18rem]">
            <div class="min-w-0 space-y-5">

        {{-- Basic Info --}}
        <x-ui.card class="rounded-2xl border-gray-200/80 p-5 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 sm:p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <x-icon name="document-text" class="w-5 h-5 text-primary-500" />
                {{ __('Informasi Template') }}
            </h2>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Nama Template') }} <span class="text-danger-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $template->name ?? '') }}" required
                        placeholder="{{ __('contoh: PO Langganan Cloud Bulanan') }}"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    @error('name')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Kode') }} <span class="text-danger-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $template->code ?? '') }}" required
                        placeholder="{{ __('contoh: PO-CLOUD') }}" maxlength="50"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10 uppercase">
                    @error('code')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Jenis Approval') }} <span class="text-danger-500">*</span></label>
                    <select name="approval_type" required x-model="approvalType"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                        <option value="only">{{ __('Approval Only (Memo, SOP, Surat Tugas)') }}</option>
                        <option value="po">{{ __('Approval to PO (Purchase Order)') }}</option>
                        <option value="invoice">{{ __('Approval to Invoice (Tagihan)') }}</option>
                        <option value="loan">{{ __('Employee Loan (Pinjaman)') }}</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Deskripsi Template') }}</label>
                    <textarea name="description" rows="2" placeholder="{{ __('Deskripsi singkat kapan template ini digunakan...') }}"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">{{ old('description', $template->description ?? '') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Syarat & Ketentuan (Opsional)') }}</label>
                    <textarea name="fields[terms_and_conditions]" rows="4" placeholder="{{ __('Ketikkan syarat dan ketentuan untuk dokumen ini. Setiap baris (enter) akan otomatis dijadikan penomoran list (1, 2, 3) di PDF.') }}"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">{{ old('fields.terms_and_conditions', $fields['terms_and_conditions'] ?? '') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Kosongkan jika template ini tidak membutuhkan Syarat & Ketentuan. Gunakan enter untuk memisahkan antar poin (nomor).') }}</p>
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $template->is_active ?? true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500/20">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Template Aktif') }}</span>
                    </label>
                </div>
            </div>
        </x-ui.card>

        {{-- Default Values: Common --}}
        <x-ui.card class="rounded-2xl border-gray-200/80 p-5 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 sm:p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 flex items-center gap-2">
                <x-icon name="sparkles" class="w-5 h-5 text-amber-500" />
                {{ __('Default Values — Umum') }}
            </h2>
            <p class="text-sm text-gray-500 mb-4">{{ __('Field ini akan otomatis terisi saat user memilih template') }}</p>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Judul') }}</label>
                    <input type="text" name="fields[title]" value="{{ $fields['title'] ?? '' }}"
                        placeholder="{{ __('contoh: Pengajuan Pembelian Server Cloud') }}"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    <p class="mt-1 text-xs text-gray-400">{{ __('Judul yang otomatis terisi, user bisa mengedit') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Prioritas') }}</label>
                    <select name="fields[priority]"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                        <option value="">— {{ __('Tidak diisi') }} —</option>
                        <option value="low" {{ ($fields['priority'] ?? '') === 'low' ? 'selected' : '' }}>{{ __('Rendah') }}</option>
                        <option value="normal" {{ ($fields['priority'] ?? '') === 'normal' ? 'selected' : '' }}>{{ __('Normal') }}</option>
                        <option value="high" {{ ($fields['priority'] ?? '') === 'high' ? 'selected' : '' }}>{{ __('Tinggi') }}</option>
                        <option value="urgent" {{ ($fields['priority'] ?? '') === 'urgent' ? 'selected' : '' }}>{{ __('Urgent') }}</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Deskripsi') }}</label>
                    <textarea name="fields[description]" rows="3"
                        placeholder="{{ __('Boilerplate teks yang otomatis terisi...') }}"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">{{ $fields['description'] ?? '' }}</textarea>
                </div>
            </div>
        </x-ui.card>

        {{-- Default Values: Approval Only --}}
        <div x-show="approvalType === 'only'" x-transition>
            <x-ui.card class="rounded-2xl border-gray-200/80 p-5 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 sm:p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 flex items-center gap-2">
                    <x-icon name="document-check" class="w-5 h-5 text-blue-500" />
                    {{ __('Default Values — Dokumen Administratif') }}
                </h2>
                <p class="text-sm text-gray-500 mb-4">{{ __('Khusus untuk tipe "Approval Only"') }}</p>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Kategori') }}</label>
                        <input type="text" name="fields[document_category]" list="category_options"
                            value="{{ $fields['document_category'] ?? '' }}"
                            placeholder="{{ __('Contoh: Surat Perjalanan Dinas') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                        <datalist id="category_options">
                            <option value="Memo Internal">
                            <option value="SOP / Kebijakan">
                            <option value="Surat Tugas">
                            <option value="Surat Keputusan">
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Departemen Terkait') }}</label>
                        <select name="fields[related_department]"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                            <option value="">— {{ __('Tidak diisi') }} —</option>
                            @foreach(\App\Models\Department::allActive() as $dept)
                                <option value="{{ $dept->name }}" {{ ($fields['related_department'] ?? '') === $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Default Values: PO --}}
        <div x-show="approvalType === 'po'" x-transition>
            <x-ui.card class="rounded-2xl border-gray-200/80 p-5 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 sm:p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 flex items-center gap-2">
                    <x-icon name="shopping-cart" class="w-5 h-5 text-green-500" />
                    {{ __('Default Values — Purchase Order') }}
                </h2>
                <p class="text-sm text-gray-500 mb-4">{{ __('Khusus untuk tipe "Approval to PO"') }}</p>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Nama Vendor') }}</label>
                        <input type="text" name="fields[vendor_name]" value="{{ $fields['vendor_name'] ?? '' }}"
                            placeholder="{{ __('contoh: PT Amazon Web Services') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default NPWP Vendor') }}</label>
                        <input type="text" name="fields[vendor_npwp]" value="{{ $fields['vendor_npwp'] ?? '' }}"
                            placeholder="XX.XXX.XXX.X-XXX.XXX"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Rincian Barang/Jasa') }}</label>
                        <textarea name="fields[item_description]" rows="3"
                            placeholder="1. Cloud Server x 1 unit @ Rp 5.000.000&#10;2. Domain & SSL x 1 paket @ Rp 500.000"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">{{ $fields['item_description'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Kode Anggaran') }}</label>
                        <input type="text" name="fields[budget_code]" value="{{ $fields['budget_code'] ?? '' }}"
                            placeholder="{{ __('contoh: IT-2026-CLOUD') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Default Values: Invoice --}}
        <div x-show="approvalType === 'invoice'" x-transition>
            <x-ui.card class="rounded-2xl border-gray-200/80 p-5 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 sm:p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 flex items-center gap-2">
                    <x-icon name="receipt-percent" class="w-5 h-5 text-orange-500" />
                    {{ __('Default Values — Invoice') }}
                </h2>
                <p class="text-sm text-gray-500 mb-4">{{ __('Khusus untuk tipe "Approval to Invoice"') }}</p>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Nama Vendor') }}</label>
                        <input type="text" name="fields[vendor_name_invoice]" value="{{ $fields['vendor_name_invoice'] ?? '' }}"
                            placeholder="{{ __('contoh: PT Telkom Indonesia') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Rekening Tujuan') }}</label>
                        <input type="text" name="fields[bank_account]" value="{{ $fields['bank_account'] ?? '' }}"
                            placeholder="BCA - 1234567890 a.n. PT Vendor"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default No. Faktur Pajak') }}</label>
                        <input type="text" name="fields[tax_invoice]" value="{{ $fields['tax_invoice'] ?? '' }}"
                            placeholder="010.000-26.00000001"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Default Values: Loan --}}
        <div x-show="approvalType === 'loan'" x-transition>
            <x-ui.card class="rounded-2xl border-gray-200/80 p-5 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 sm:p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 flex items-center gap-2">
                    <x-icon name="currency-dollar" class="w-5 h-5 text-purple-500" />
                    {{ __('Default Values — Pinjaman') }}
                </h2>
                <p class="text-sm text-gray-500 mb-4">{{ __('Khusus untuk tipe "Employee Loan"') }}</p>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Jenis Pinjaman') }}</label>
                        <select name="fields[loan_type]"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                            <option value="">— {{ __('Tidak diisi') }} —</option>
                            <option value="darurat" {{ ($fields['loan_type'] ?? '') === 'darurat' ? 'selected' : '' }}>{{ __('Pinjaman Darurat') }}</option>
                            <option value="pendidikan" {{ ($fields['loan_type'] ?? '') === 'pendidikan' ? 'selected' : '' }}>{{ __('Pinjaman Pendidikan') }}</option>
                            <option value="perumahan" {{ ($fields['loan_type'] ?? '') === 'perumahan' ? 'selected' : '' }}>{{ __('Pinjaman Perumahan') }}</option>
                            <option value="kendaraan" {{ ($fields['loan_type'] ?? '') === 'kendaraan' ? 'selected' : '' }}>{{ __('Pinjaman Kendaraan') }}</option>
                            <option value="lainnya" {{ ($fields['loan_type'] ?? '') === 'lainnya' ? 'selected' : '' }}>{{ __('Lainnya') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Cicilan') }}</label>
                        <select name="fields[installment_count]"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                            <option value="">— {{ __('Tidak diisi') }} —</option>
                            <option value="3" {{ ($fields['installment_count'] ?? '') === '3' ? 'selected' : '' }}>{{ __('3 Bulan') }}</option>
                            <option value="6" {{ ($fields['installment_count'] ?? '') === '6' ? 'selected' : '' }}>{{ __('6 Bulan') }}</option>
                            <option value="12" {{ ($fields['installment_count'] ?? '') === '12' ? 'selected' : '' }}>{{ __('12 Bulan') }}</option>
                            <option value="24" {{ ($fields['installment_count'] ?? '') === '24' ? 'selected' : '' }}>{{ __('24 Bulan') }}</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Alasan') }}</label>
                        <textarea name="fields[loan_reason]" rows="2"
                            placeholder="{{ __('Boilerplate alasan pinjaman...') }}"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">{{ $fields['loan_reason'] ?? '' }}</textarea>
                    </div>
                </div>
            </x-ui.card>
        </div>
        {{-- Dynamic Form Builder --}}
        <x-ui.card class="rounded-2xl border-gray-200/80 p-5 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 sm:p-6" x-data="{
            dynamicFields: {{ Js::from($fields['dynamic_fields'] ?? []) }},
            init() {
                this.dynamicFields.forEach(f => {
                    if (!f.id) f.id = Date.now().toString() + Math.random().toString(36).substr(2, 5);
                });
            },
            addField() {
                this.dynamicFields.push({
                    id: Date.now().toString(),
                    name: '',
                    label: '',
                    type: 'text',
                    required: true,
                    options: '',
                    columns: ''
                });
            },
            removeField(index) {
                this.dynamicFields.splice(index, 1);
            },
            moveFieldUp(index) {
                if (index > 0) {
                    const el = this.dynamicFields[index];
                    this.dynamicFields.splice(index, 1);
                    this.dynamicFields.splice(index - 1, 0, el);
                }
            },
            moveFieldDown(index) {
                if (index < this.dynamicFields.length - 1) {
                    const el = this.dynamicFields[index];
                    this.dynamicFields.splice(index, 1);
                    this.dynamicFields.splice(index + 1, 0, el);
                }
            }
        }">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="queue-list" class="w-5 h-5 text-pink-500" />
                    {{ __('Dynamic Form Builder') }}
                </h2>
                <button type="button" @click="addField"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-pink-50 text-pink-600 hover:bg-pink-100 rounded-xl text-sm font-medium transition-colors">
                    <x-icon name="plus" class="w-4 h-4" /> {{ __('Tambah Field') }}
                </button>
            </div>
            
            <p class="text-sm text-gray-500 mb-4">
                {{ __('Tambahkan input khusus yang akan diisi oleh user saat menggunakan template ini.') }}<br>
                {{ __('Field ini akan otomatis muncul pada form pengajuan dokumen.') }}
            </p>

            <div class="space-y-4">
                <template x-for="(field, index) in dynamicFields" :key="field.id || index">
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 relative group">
                        <div class="absolute top-3 right-3 flex items-center gap-1">
                            <button type="button" @click="moveFieldUp(index)" x-show="index > 0" class="p-1 text-gray-400 hover:text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/30 rounded-md transition-colors tooltip" data-tip="{{ __('Geser ke Atas') }}">
                                <x-icon name="chevron-up" class="w-5 h-5" />
                            </button>
                            <button type="button" @click="moveFieldDown(index)" x-show="index < dynamicFields.length - 1" class="p-1 text-gray-400 hover:text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/30 rounded-md transition-colors tooltip" data-tip="{{ __('Geser ke Bawah') }}">
                                <x-icon name="chevron-down" class="w-5 h-5" />
                            </button>
                            <button type="button" @click="removeField(index)" class="p-1 text-gray-400 hover:text-danger-500 hover:bg-danger-50 dark:hover:bg-danger-900/30 rounded-md transition-colors tooltip ml-1" data-tip="{{ __('Hapus Field') }}">
                                <x-icon name="trash" class="w-5 h-5" />
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 pr-8">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Label Input') }} <span class="text-danger-500">*</span></label>
                                <input type="text" x-model="field.label" :name="`fields[dynamic_fields][${index}][label]`" required placeholder="{{ __('Cth: Nama Pengemudi') }}" class="block w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Field Name (Internal)') }} <span class="text-danger-500">*</span></label>
                                <input type="text" x-model="field.name" :name="`fields[dynamic_fields][${index}][name]`" required placeholder="cth: nama_pengemudi" class="block w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                <p class="text-[10px] text-gray-500 mt-1">{{ __('Tanpa spasi, gunakan huruf kecil dan underscore') }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Tipe Input') }} <span class="text-danger-500">*</span></label>
                                <select x-model="field.type" :name="`fields[dynamic_fields][${index}][type]`" required class="block w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                    <option value="text">{{ __('Teks Pendek') }}</option>
                                    <option value="textarea">{{ __('Teks Panjang') }}</option>
                                    <option value="number">{{ __('Angka') }}</option>
                                    <option value="date">{{ __('Tanggal') }}</option>
                                    <option value="time">{{ __('Jam') }}</option>
                                    <option value="select">{{ __('Dropdown (Pilihan)') }}</option>
                                    <option value="file">{{ __('Upload File/Foto') }}</option>
                                    <option value="table">{{ __('Tabel Dinamis') }}</option>
                                </select>
                            </div>
                            <div class="flex items-center mt-5">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="hidden" :name="`fields[dynamic_fields][${index}][required]`" value="0">
                                    <input type="checkbox" x-model="field.required" :name="`fields[dynamic_fields][${index}][required]`" value="1" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Wajib Diisi (Required)') }}</span>
                                </label>
                            </div>
                            
                            <!-- Options for Select -->
                            <div x-show="field.type === 'select'" class="md:col-span-2 lg:col-span-4 mt-2">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Opsi Dropdown') }} <span class="text-gray-400 font-normal">({{ __('Pisahkan dengan koma atau enter') }})</span></label>
                                <textarea x-model="field.options" :name="`fields[dynamic_fields][${index}][options]`" rows="2" placeholder="{{ __('Cth: Innova, Avanza, Xpander') }}" class="block w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"></textarea>
                            </div>
                            
                            <!-- Columns for Table -->
                            <div x-show="field.type === 'table'" class="md:col-span-2 lg:col-span-4 mt-2">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Kolom Tabel') }} <span class="text-gray-400 font-normal">({{ __('Pisahkan dengan koma') }})</span></label>
                                <input type="text" x-model="field.columns" :name="`fields[dynamic_fields][${index}][columns]`" placeholder="{{ __('Cth: Nama Barang, Jumlah, Harga') }}" class="block w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>
                </template>
                
                <div x-show="dynamicFields.length === 0" class="text-center py-6 border-2 border-dashed border-gray-200/80 dark:border-gray-700/80 rounded-xl">
                    <p class="text-sm text-gray-500 mb-2">{{ __('Belum ada input khusus (dynamic field) untuk template ini.') }}</p>
                </div>
            </div>
        </x-ui.card>

        {{-- Workflow / Approver Chain --}}
        <x-ui.card class="rounded-2xl border-gray-200/80 p-5 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="user-group" class="w-5 h-5 text-indigo-500" />
                    {{ __('Susunan Approver (Wajib)') }}
                </h2>
                <button type="button" @click="addWorkflow"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-xl text-sm font-medium transition-colors">
                    <x-icon name="plus" class="w-4 h-4" /> {{ __('Tambah Approver') }}
                </button>
            </div>
            
            <p class="text-sm text-gray-500 mb-4">
                {{ __('Tentukan urutan persetujuan dokumen. Sistem akan memproses persetujuan sesuai urutan dari atas ke bawah.') }}
            </p>

            @error('workflow')<p class="mb-4 text-sm text-danger-600">{{ $message }}</p>@enderror

            <div class="space-y-3" x-show="workflows.length > 0">
                <template x-for="(role, index) in workflows" :key="index">
                    <div class="flex items-center gap-3 p-3 bg-gray-50/80 dark:bg-gray-800 border border-gray-200/80 dark:border-gray-700/80 rounded-xl relative group">
                        <!-- Step Indicator -->
                        <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center shrink-0">
                            <span class="text-indigo-600 dark:text-indigo-400 font-bold text-sm" x-text="index + 1"></span>
                        </div>

                        <!-- Role Select -->
                        <div class="flex-1">
                            <select x-model="workflows[index]" :name="`workflow[${index + 1}]`" required
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="" disabled>-- {{ __('Pilih Approver') }} --</option>
                                <optgroup label="{{ __('Berdasarkan Role/Jabatan') }}">
                                    @foreach($roles as $roleData)
                                        <option value="role:{{ $roleData->name }}">{{ role_label($roleData->name) }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="{{ __('Berdasarkan Spesifik User') }}">
                                    @foreach($users as $u)
                                        @php
                                            $position = $u->position ?? 'Tanpa Posisi';
                                            $dept = $u->department ?? 'No Dept';
                                        @endphp
                                        <option value="user:{{ $u->id }}">{{ $u->name }} ({{ $position }} - {{ $dept }})</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button" @click="moveWorkflowUp(index)" x-show="index > 0" class="p-2 text-gray-400 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-colors tooltip" data-tip="{{ __('Geser ke Atas') }}">
                                <x-icon name="chevron-up" class="w-5 h-5" />
                            </button>
                            <button type="button" @click="moveWorkflowDown(index)" x-show="index < workflows.length - 1" class="p-2 text-gray-400 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-colors tooltip" data-tip="{{ __('Geser ke Bawah') }}">
                                <x-icon name="chevron-down" class="w-5 h-5" />
                            </button>
                            <button type="button" @click="removeWorkflow(index)"
                                class="p-2 text-gray-400 hover:text-danger-500 hover:bg-danger-50 rounded-xl transition-colors tooltip ml-1"
                                data-tip="{{ __('Hapus') }}">
                                <x-icon name="trash" class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="workflows.length === 0" class="text-center py-6 border-2 border-dashed border-gray-200/80 dark:border-gray-700/80 rounded-xl">
                <p class="text-sm text-gray-500">{{ __('Belum ada approver yang ditambahkan.') }}</p>
                <button type="button" @click="addWorkflow" class="mt-2 text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                    + {{ __('Tambah Approver Pertama') }}
                </button>
            </div>
        </x-ui.card>


            </div>

            <aside class="space-y-4 xl:sticky xl:top-6">
                <div class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 dark:bg-gray-900">
                    <div class="border-b border-gray-200/80 bg-primary-50/60 px-5 py-4 dark:border-gray-800/80 dark:bg-primary-900/10">
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-400">{{ __('Configuration map') }}</p>
                        <h2 class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ __('Bangun template bertahap') }}</h2>
                    </div>
                    <div class="space-y-1 p-3">
                        <div class="flex items-start gap-3 rounded-xl bg-gray-50 px-3 py-3 dark:bg-gray-800/70">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-primary-100 text-[10px] font-bold text-primary-700 dark:bg-primary-900/40 dark:text-primary-300">01</span>
                            <div><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ __('Identitas') }}</p><p class="mt-0.5 text-[10px] leading-4 text-gray-500">{{ __('Nama, kode, tipe, dan status') }}</p></div>
                        </div>
                        <div class="flex items-start gap-3 rounded-xl px-3 py-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-[10px] font-bold text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">02</span>
                            <div><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ __('Default values') }}</p><p class="mt-0.5 text-[10px] leading-4 text-gray-500">{{ __('Nilai awal sesuai tipe approval') }}</p></div>
                        </div>
                        <div class="flex items-start gap-3 rounded-xl px-3 py-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-[10px] font-bold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">03</span>
                            <div><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ __('Workflow') }}</p><p class="mt-0.5 text-[10px] leading-4 text-gray-500">{{ __('Field khusus dan approver chain') }}</p></div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-primary-100 bg-primary-50/60 p-4 dark:border-primary-900/40 dark:bg-primary-900/10">
                    <div class="flex items-center gap-2 text-primary-800 dark:text-primary-300">
                        <x-icon name="information-circle" class="h-4 w-4" />
                        <p class="text-xs font-semibold">{{ __('Catatan penting') }}</p>
                    </div>
                    <p class="mt-2 text-xs leading-5 text-primary-700/80 dark:text-primary-300/70">{{ __('Susunan approver wajib memiliki minimal satu langkah agar template dapat disimpan.') }}</p>
                </div>
            </aside>
        </div>

        {{-- Actions --}}
        <div class="sticky bottom-4 z-20 flex flex-wrap items-center justify-end gap-3 rounded-2xl border border-gray-200/80 bg-white/95 p-3 shadow-soft-lg backdrop-blur dark:border-gray-800/80 dark:bg-gray-900/95">
            <x-ui.button type="button" variant="secondary" href="{{ route('admin.templates.index') }}">{{ __('Batal') }}</x-ui.button>
            <x-ui.button type="submit" icon="check">{{ __('Simpan Template') }}</x-ui.button>
        </div>
    </form>
</x-app-layout>