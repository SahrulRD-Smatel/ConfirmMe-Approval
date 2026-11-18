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
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Konfigurasi alur kerja, formulir isian dinamis, dan persetujuan dokumen.') }}</p>
            </div>
        </div>
    </x-slot>

    @php
        $fields = old('fields', $template->fields ?? []);
    @endphp

    <form action="{{ isset($template) ? route('admin.templates.update', $template) : route('admin.templates.store') }}"
        method="POST" class="mx-auto max-w-7xl space-y-6" x-data="{
            approvalType: '{{ old('approval_type', $template->approval_type ?? 'only') }}',
            workflows: @php
                $rawWorkflows = old('workflow', isset($template) ? array_values((array) ($template->workflow ?? [])) : []);
                $rawSigners = old('fields.workflow_signers', isset($template) ? ($template->fields['workflow_signers'] ?? []) : []);
                $combinedWorkflows = [];
                foreach ($rawWorkflows as $i => $w) {
                    $combinedWorkflows[] = [
                        'target' => is_array($w) ? ($w['target'] ?? '') : (string)$w,
                        'show_in_pdf' => isset($rawSigners[$i]['show_in_pdf']) ? (bool)$rawSigners[$i]['show_in_pdf'] : true,
                        'pdf_label' => $rawSigners[$i]['pdf_label'] ?? '',
                    ];
                }
                echo Js::from($combinedWorkflows);
            @endphp,
            showAdvancedDefaults: {{ (isset($template) && (!empty($fields['title']) || !empty($fields['description']) || !empty($fields['document_category']) || !empty($fields['terms_and_conditions']) || !empty($fields['priority']) || !empty($fields['related_department']) || !empty($fields['vendor_name']) || !empty($fields['budget_code']) || !empty($fields['loan_type']))) ? 'true' : 'false' }},
            init() {
                if (!Array.isArray(this.workflows)) {
                    this.workflows = Object.values(this.workflows || {});
                }
                this.workflows = this.workflows.map(item => {
                    let target = typeof item === 'object' && item !== null ? (item.target || '') : (item || '');
                    if (target && !target.startsWith('role:') && !target.startsWith('user:') && !target.startsWith('hierarchy:')) {
                        target = 'role:' + target;
                    }
                    return {
                        target: target,
                        show_in_pdf: (typeof item === 'object' && item !== null && 'show_in_pdf' in item) ? Boolean(item.show_in_pdf) : true,
                        pdf_label: (typeof item === 'object' && item !== null && 'pdf_label' in item) ? (item.pdf_label || '') : '',
                    };
                });
            },
            addWorkflow() {
                this.workflows.push({ target: '', show_in_pdf: true, pdf_label: '' });
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
        @submit="if(workflows.length === 0 || workflows.every(w => !w.target)) { window.showAlert('{{ __('Harap tentukan minimal 1 approver pada Susunan Approver!') }}', 'warning'); $event.preventDefault(); }">
        @csrf
        @if(isset($template)) @method('PUT') @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-danger-200 bg-danger-50 p-4 dark:border-danger-800/80 dark:bg-danger-900/20">
                <div class="flex items-start gap-3">
                    <x-icon name="exclamation-triangle" class="h-5 w-5 shrink-0 text-danger-500 mt-0.5" />
                    <div>
                        <h3 class="text-sm font-bold text-danger-800 dark:text-danger-200">{{ __('Gagal menyimpan template:') }}</h3>
                        <ul class="mt-1.5 list-inside list-disc text-sm text-danger-700 dark:text-danger-300 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 items-start gap-5 xl:grid-cols-[minmax(0,1fr)_18rem]">
            <div class="min-w-0 space-y-5">

                {{-- 1. IDENTITAS TEMPLATE --}}
                <x-ui.card class="rounded-2xl border-gray-200/80 p-5 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 sm:p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <x-icon name="document-text" class="w-5 h-5 text-primary-500" />
                        {{ __('Informasi Template') }}
                    </h2>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Nama Template') }} <span class="text-danger-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $template->name ?? '') }}" required
                                placeholder="{{ __('contoh: SURAT TUGAS PERJALANAN DINAS') }}"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20">
                            @error('name')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Kode') }} <span class="text-danger-500">*</span></label>
                            <input type="text" name="code" value="{{ old('code', $template->code ?? '') }}" required
                                placeholder="{{ __('contoh: STPD') }}" maxlength="50"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20 uppercase">
                            @error('code')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Jenis Approval') }} <span class="text-danger-500">*</span></label>
                            <select name="approval_type" required x-model="approvalType"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20">
                                <option value="only">{{ __('Approval Only (Memo, SOP, Surat Tugas)') }}</option>
                                <option value="po">{{ __('Approval to PO (Purchase Order)') }}</option>
                                <option value="invoice">{{ __('Approval to Invoice (Tagihan)') }}</option>
                                <option value="loan">{{ __('Employee Loan (Pinjaman)') }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Tipe Layout PDF') }}</label>
                            <select name="fields[pdf_layout]"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20">
                                <option value="auto" {{ ($fields['pdf_layout'] ?? 'auto') === 'auto' ? 'selected' : '' }}>{{ __('Otomatis (Sesuai jenis isian form)') }}</option>
                                <option value="formulir_kerja" {{ ($fields['pdf_layout'] ?? '') === 'formulir_kerja' ? 'selected' : '' }}>{{ __('Formulir Kerja Resmi (Portrait - Peminjaman, Izin, Tugas, dll.)') }}</option>
                                <option value="tabel_pengadaan" {{ ($fields['pdf_layout'] ?? '') === 'tabel_pengadaan' ? 'selected' : '' }}>{{ __('Daftar Pengadaan / Pembelian (Landscape - Tabel Barang & Harga)') }}</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-400">{{ __('Format tata letak cetak dokumen PDF yang dihasilkan.') }}</p>
                        </div>

                        <div class="flex items-center pt-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $template->is_active ?? true) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500/20">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Template Aktif') }}</span>
                            </label>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Deskripsi Template') }}</label>
                            <textarea name="description" rows="2" placeholder="{{ __('Deskripsi singkat untuk kartu katalog template...') }}"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20">{{ old('description', $template->description ?? '') }}</textarea>
                            <p class="mt-1 text-xs text-gray-400">{{ __('Ditampilkan di kartu katalog saat pemohon memilih modul dokumen.') }}</p>
                        </div>
                    </div>
                </x-ui.card>

                {{-- 2. DYNAMIC FORM BUILDER --}}
                <x-ui.card class="rounded-2xl border-gray-200/80 p-5 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 sm:p-6" x-data="{
                    dynamicFields: {{ Js::from($fields['dynamic_fields'] ?? []) }},
                    init() {
                        this.dynamicFields.forEach(f => {
                            if (!f.id) f.id = Date.now().toString() + Math.random().toString(36).substr(2, 5);
                        });
                    },
                    addField() {
                        this.dynamicFields.push({
                            id: Date.now().toString() + Math.random().toString(36).substr(2, 5),
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
                    <div class="flex flex-wrap items-start justify-between gap-3 mb-2">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <x-icon name="queue-list" class="w-5 h-5 text-primary-600" />
                            {{ __('Isian Tambahan pada Form Pengajuan') }}
                        </h2>
                        <button type="button" @click="addField"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-primary-600 text-white hover:bg-primary-700 rounded-xl text-sm font-semibold transition-colors">
                            <x-icon name="plus" class="w-4 h-4" /> {{ __('Tambah Field') }}
                        </button>
                    </div>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        {{ __('Tambahkan input khusus yang akan diisi oleh user saat menggunakan template ini. Field ini akan otomatis muncul pada form pengajuan dokumen.') }}
                    </p>

                    <ol class="mb-5 grid gap-2 sm:grid-cols-3">
                        <li class="flex items-start gap-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 px-3 py-2.5">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-primary-600 text-[11px] font-bold text-white">1</span>
                            <span class="text-xs leading-relaxed text-gray-600 dark:text-gray-300">{{ __('Klik Tambah Field untuk membuat isian baru.') }}</span>
                        </li>
                        <li class="flex items-start gap-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 px-3 py-2.5">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-primary-600 text-[11px] font-bold text-white">2</span>
                            <span class="text-xs leading-relaxed text-gray-600 dark:text-gray-300">{{ __('Tulis label yang dilihat pengaju, kode field, lalu pilih tipe isiannya.') }}</span>
                        </li>
                        <li class="flex items-start gap-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 px-3 py-2.5">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-primary-600 text-[11px] font-bold text-white">3</span>
                            <span class="text-xs leading-relaxed text-gray-600 dark:text-gray-300">{{ __('Atur urutan dengan panah naik/turun — urutan di sini sama dengan urutan tampil di form pengajuan.') }}</span>
                        </li>
                    </ol>

                    <div class="space-y-4">
                        <template x-for="(field, index) in dynamicFields" :key="field.id || index">
                            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                <div class="flex items-center justify-between gap-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 px-4 py-2.5">
                                    <div class="flex min-w-0 items-center gap-2.5">
                                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary-600 text-xs font-bold text-white" x-text="index + 1"></span>
                                        <span class="truncate text-sm font-semibold text-gray-800 dark:text-gray-100" x-text="field.label || '{{ __('Field Baru') }}'"></span>
                                        <span x-show="field.required" class="hidden shrink-0 rounded-md bg-primary-50 px-1.5 py-0.5 text-[11px] font-semibold text-primary-700 dark:bg-primary-900/30 dark:text-primary-300 sm:inline">{{ __('Wajib') }}</span>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-1">
                                        <button type="button" @click="moveFieldUp(index)" :class="{'invisible pointer-events-none': index === 0}" class="p-1.5 text-gray-400 hover:text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/30 rounded-md transition-colors tooltip" data-tip="{{ __('Geser ke Atas') }}">
                                            <x-icon name="chevron-up" class="w-5 h-5" />
                                        </button>
                                        <button type="button" @click="moveFieldDown(index)" :class="{'invisible pointer-events-none': index === dynamicFields.length - 1}" class="p-1.5 text-gray-400 hover:text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/30 rounded-md transition-colors tooltip" data-tip="{{ __('Geser ke Bawah') }}">
                                            <x-icon name="chevron-down" class="w-5 h-5" />
                                        </button>
                                        <button type="button" @click="removeField(index)" class="ml-1 inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-xs font-medium text-danger-500 hover:bg-danger-50 dark:hover:bg-danger-900/30 transition-colors tooltip" data-tip="{{ __('Hapus Field') }}">
                                            <x-icon name="trash" class="w-4 h-4" /> <span class="hidden sm:inline">{{ __('Hapus') }}</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="space-y-4 p-4">
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Label Input') }} <span class="text-danger-500">*</span></label>
                                            <input type="text" x-model="field.label" :name="`fields[dynamic_fields][${index}][label]`" required placeholder="{{ __('Cth: Nama Pengemudi') }}" class="block w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                            <p class="text-xs text-gray-400 mt-1">{{ __('Nama isian yang dilihat pengaju saat mengisi form.') }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Field Name (Internal)') }} <span class="text-danger-500">*</span></label>
                                            <input type="text" x-model="field.name" :name="`fields[dynamic_fields][${index}][name]`" required placeholder="cth: nama_pengemudi" class="block w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                            <p class="text-xs text-gray-400 mt-1">{{ __('Kode unik field ini, dipakai untuk menyisipkan isian ke isi surat. Tanpa spasi, gunakan huruf kecil dan underscore.') }}</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
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
                                            <p class="text-xs text-gray-400 mt-1">{{ __('Menentukan cara pengaju mengisi field ini.') }}</p>
                                        </div>
                                        <div>
                                            <span class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Wajib Diisi (Required)') }}</span>
                                            <label class="flex cursor-pointer items-start gap-2.5 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600">
                                                <input type="hidden" :name="`fields[dynamic_fields][${index}][required]`" value="0">
                                                <input type="checkbox" x-model="field.required" :name="`fields[dynamic_fields][${index}][required]`" value="1" class="mt-0.5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 w-4 h-4">
                                                <span class="text-xs leading-relaxed text-gray-500 dark:text-gray-400">{{ __('Aktifkan jika pengaju tidak boleh mengirim pengajuan sebelum field ini diisi.') }}</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Options for Select -->
                                    <div x-show="field.type === 'select'" class="rounded-lg border border-primary-100 bg-primary-50/50 p-3 dark:border-primary-900/40 dark:bg-primary-900/10">
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Opsi Dropdown') }} <span class="text-gray-400 font-normal">({{ __('Pisahkan dengan koma atau enter') }})</span></label>
                                        <textarea x-model="field.options" :name="`fields[dynamic_fields][${index}][options]`" rows="2" placeholder="{{ __('Cth: Innova, Avanza, Xpander') }}" class="block w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"></textarea>
                                        <p class="text-xs text-gray-400 mt-1">{{ __('Pengaju memilih satu dari opsi yang Anda tulis di sini.') }}</p>
                                    </div>

                                    <!-- Columns for Table -->
                                    <div x-show="field.type === 'table'" class="rounded-lg border border-primary-100 bg-primary-50/50 p-3 dark:border-primary-900/40 dark:bg-primary-900/10">
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Kolom Tabel') }} <span class="text-gray-400 font-normal">({{ __('Pisahkan dengan koma') }})</span></label>
                                        <input type="text" x-model="field.columns" :name="`fields[dynamic_fields][${index}][columns]`" placeholder="{{ __('Cth: Nama Barang, Jumlah, Harga') }}" class="block w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                                        <p class="text-xs text-gray-400 mt-1">{{ __('Pengaju akan mengisi baris-baris tabel dengan kolom ini, misalnya daftar barang.') }}</p>
                                    </div>

                                    <div class="rounded-lg border border-dashed border-gray-200 bg-gray-50/70 px-3 py-3 dark:border-gray-700 dark:bg-gray-900/40">
                                        <p class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400">{{ __('Pratinjau — tampilan untuk pengaju') }}</p>
                                        <p class="mb-1 text-xs font-medium text-gray-600 dark:text-gray-300">
                                            <span x-text="field.label || '{{ __('Label Input') }}'"></span> <span x-show="field.required" class="text-danger-500">*</span>
                                        </p>
                                        <div x-show="field.type === 'text' || field.type === 'number' || field.type === 'date' || field.type === 'time'" class="flex h-9 items-center rounded-lg border border-gray-200 bg-white px-3 text-xs text-gray-400 dark:border-gray-600 dark:bg-gray-700">
                                            <span x-show="field.type === 'text'">{{ __('Kotak teks satu baris') }}</span>
                                            <span x-show="field.type === 'number'">{{ __('Kotak khusus angka') }}</span>
                                            <span x-show="field.type === 'date'">{{ __('Pemilih tanggal') }}</span>
                                            <span x-show="field.type === 'time'">{{ __('Pemilih jam (Format 24 Jam WIB)') }}</span>
                                        </div>
                                        <div x-show="field.type === 'textarea'" class="flex h-16 items-start rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-400 dark:border-gray-600 dark:bg-gray-700">{{ __('Kotak teks panjang (beberapa baris)') }}</div>
                                        <div x-show="field.type === 'select'" class="flex h-9 items-center justify-between rounded-lg border border-gray-200 bg-white px-3 text-xs text-gray-400 dark:border-gray-600 dark:bg-gray-700">
                                            <span class="truncate" x-text="field.options ? field.options.split(',')[0].trim() : '{{ __('Pilih salah satu opsi') }}'"></span>
                                            <x-icon name="chevron-down" class="w-4 h-4 shrink-0" />
                                        </div>
                                        <div x-show="field.type === 'file'" class="flex h-9 items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 text-xs text-gray-400 dark:border-gray-600 dark:bg-gray-700">
                                            <x-icon name="arrow-up-tray" class="w-4 h-4" /> {{ __('Tombol unggah file/foto') }}
                                        </div>
                                        <div x-show="field.type === 'table'" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-400 dark:border-gray-600 dark:bg-gray-700">
                                            <span x-text="field.columns ? field.columns.split(',').map(c => c.trim()).join('  ·  ') : '{{ __('Kolom tabel mengikuti isian Kolom Tabel di atas') }}'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="dynamicFields.length === 0" class="rounded-xl border-2 border-dashed border-gray-200/80 px-4 py-8 text-center dark:border-gray-700/80">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-1">{{ __('Belum ada input khusus (dynamic field) untuk template ini.') }}</p>
                            <p class="text-xs text-gray-400 mb-4">{{ __('Tanpa isian tambahan, pengaju hanya mengisi data umum pengajuan.') }}</p>
                            <button type="button" @click="addField"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-primary-600 text-white hover:bg-primary-700 rounded-xl text-sm font-semibold transition-colors">
                                <x-icon name="plus" class="w-4 h-4" /> {{ __('Tambah Field') }}
                            </button>
                        </div>

                        <button type="button" @click="addField" x-show="dynamicFields.length > 0"
                            class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl border-2 border-dashed border-gray-200 py-3 text-sm font-medium text-gray-500 transition-colors hover:border-primary-300 hover:text-primary-600 dark:border-gray-700 dark:text-gray-400 dark:hover:border-primary-700">
                            <x-icon name="plus" class="w-4 h-4" /> {{ __('Tambah Field Lagi') }}
                        </button>
                    </div>
                </x-ui.card>

                {{-- 3. SUSUNAN APPROVER (WAJIB) --}}
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
                            <div class="p-3.5 bg-gray-50/80 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 rounded-xl space-y-2.5 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-lg bg-primary-50 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 font-bold text-xs flex items-center justify-center shrink-0 border border-primary-200/60 dark:border-primary-800/60">
                                        <span x-text="index + 1"></span>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <select x-model="workflows[index].target" name="workflow[]" required
                                            class="block w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                                            <option value="" disabled>-- {{ __('Pilih Approver') }} --</option>
                                            <optgroup label="{{ __('Hierarki Dinamis (Berdasarkan Pemohon)') }}">
                                                <option value="hierarchy:atasan_langsung">{{ __('Atasan Langsung (Dinamis: Staff -> Manager -> Direktur -> Presdir)') }}</option>
                                                <option value="hierarchy:direktur">{{ __('Direktur Divisi (Dinamis: Sesuai Departemen Pemohon)') }}</option>
                                            </optgroup>
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

                                    <div class="flex items-center gap-1 shrink-0">
                                        <button type="button" @click="moveWorkflowUp(index)" :class="{'invisible pointer-events-none': index === 0}" class="p-1.5 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-200/60 dark:hover:bg-gray-700 rounded-lg transition-colors tooltip" data-tip="{{ __('Geser ke Atas') }}">
                                            <x-icon name="chevron-up" class="w-4 h-4" />
                                        </button>
                                        <button type="button" @click="moveWorkflowDown(index)" :class="{'invisible pointer-events-none': index === workflows.length - 1}" class="p-1.5 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-200/60 dark:hover:bg-gray-700 rounded-lg transition-colors tooltip" data-tip="{{ __('Geser ke Bawah') }}">
                                            <x-icon name="chevron-down" class="w-4 h-4" />
                                        </button>
                                        <button type="button" @click="removeWorkflow(index)"
                                            class="p-1.5 text-gray-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/30 rounded-lg transition-colors tooltip ml-1"
                                            data-tip="{{ __('Hapus') }}">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>

                                {{-- Pengaturan Cetak Tanda Tangan di PDF --}}
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 pl-10 pt-2 border-t border-gray-200/60 dark:border-gray-700/60">
                                    <input type="hidden" :name="'fields[workflow_signers][' + index + '][show_in_pdf]'" value="0">
                                    <label class="inline-flex items-center gap-2 cursor-pointer select-none shrink-0">
                                        <input type="checkbox" :name="'fields[workflow_signers][' + index + '][show_in_pdf]'" value="1"
                                            x-model="workflows[index].show_in_pdf"
                                            class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500 h-4 w-4">
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Tampilkan TTD di PDF') }}</span>
                                    </label>

                                    <div class="flex-1" x-show="workflows[index].show_in_pdf">
                                        <input type="text" :name="'fields[workflow_signers][' + index + '][pdf_label]'"
                                            x-model="workflows[index].pdf_label"
                                            placeholder="{{ __('Label Jabatan di PDF (Opsional, cth: Atasan Langsung / Purchasing / IT Internal)') }}"
                                            class="block w-full px-2.5 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                    </div>
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

                {{-- 4. PIHAK MENGETAHUI / TEMBUSAN (OPSIONAL) --}}
                @php
                    $notifyRoles = old('fields.notify_roles', $fields['notify_roles'] ?? []);
                    if (!is_array($notifyRoles)) {
                        $notifyRoles = [];
                    }
                @endphp
                <x-ui.card class="rounded-2xl border-gray-200/80 p-5 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 sm:p-6"
                    x-data="{
                        notifiers: {{ Js::from(array_values($notifyRoles)) }},
                        addNotifier() {
                            this.notifiers.push('');
                        },
                        removeNotifier(index) {
                            this.notifiers.splice(index, 1);
                        }
                    }">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <x-icon name="bell" class="w-5 h-5 text-amber-500" />
                            {{ __('Pihak Mengetahui / Tembusan (Opsional)') }}
                        </h2>
                        <button type="button" @click="addNotifier"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 hover:bg-amber-100 dark:bg-amber-900/30 dark:text-amber-300 rounded-xl text-sm font-medium transition-colors">
                            <x-icon name="plus" class="w-4 h-4" /> {{ __('Tambah Pihak Mengetahui') }}
                        </button>
                    </div>
                    
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        {{ __('Pihak yang menerima notifikasi & email saat dokumen selesai disetujui (Approved), serta memiliki hak untuk melihat dokumen. Pihak ini tidak perlu menandatangani approval.') }}
                    </p>

                    <div class="space-y-3" x-show="notifiers.length > 0">
                        <template x-for="(role, index) in notifiers" :key="index">
                            <div class="flex items-center gap-3 p-3 bg-gray-50/80 dark:bg-gray-800 border border-gray-200/80 dark:border-gray-700/80 rounded-xl relative group">
                                <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center shrink-0">
                                    <x-icon name="eye" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                                </div>

                                <div class="flex-1">
                                    <select x-model="notifiers[index]" name="fields[notify_roles][]" required
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                                        <option value="" disabled>-- {{ __('Pilih Role / User') }} --</option>
                                        <optgroup label="{{ __('Hierarki Dinamis (Berdasarkan Pemohon)') }}">
                                            <option value="hierarchy:atasan_langsung">{{ __('Atasan Langsung (Dinamis)') }}</option>
                                            <option value="hierarchy:direktur">{{ __('Direktur Divisi (Dinamis)') }}</option>
                                        </optgroup>
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

                                <button type="button" @click="removeNotifier(index)"
                                    class="p-2 text-gray-400 hover:text-danger-500 hover:bg-danger-50 dark:hover:bg-danger-900/30 rounded-xl transition-colors tooltip"
                                    data-tip="{{ __('Hapus') }}">
                                    <x-icon name="trash" class="w-5 h-5" />
                                </button>
                            </div>
                        </template>
                    </div>

                    <div x-show="notifiers.length === 0" class="text-center py-6 border-2 border-dashed border-gray-200/80 dark:border-gray-700/80 rounded-xl">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Belum ada pihak mengetahui yang ditambahkan. Hanya pemohon dan para approver yang akan menerima notifikasi selesai.') }}</p>
                        <button type="button" @click="addNotifier" class="mt-2 text-amber-600 dark:text-amber-400 hover:text-amber-700 text-sm font-medium">
                            + {{ __('Tambah Pihak Mengetahui') }}
                        </button>
                    </div>
                </x-ui.card>

                {{-- 5. BANTUAN AUTO-FILL & PENGATURAN BAWAAN (OPSIONAL ACCORDION) --}}
                <x-ui.card class="rounded-2xl border-gray-200/80 p-0 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 overflow-hidden">
                    <div @click="showAdvancedDefaults = !showAdvancedDefaults"
                        class="flex cursor-pointer items-center justify-between p-5 sm:p-6 transition-colors hover:bg-gray-50/70 dark:hover:bg-gray-800/50">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 mt-0.5">
                                <x-icon name="sparkles" class="w-5 h-5" />
                            </div>
                            <div>
                                <div class="flex items-center gap-2.5">
                                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                                        {{ __('Bantuan Auto-Fill & Pengaturan Bawaan') }}
                                    </h2>
                                    <span class="rounded-md bg-gray-100 dark:bg-gray-800 px-2 py-0.5 text-[11px] font-semibold text-gray-600 dark:text-gray-300">
                                        {{ __('Opsional') }}
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 max-w-2xl leading-relaxed">
                                    {{ __('Atur teks contoh atau nilai awal agar form pemohon otomatis terisi saat template dipilih. Jika tidak diisi, pemohon akan mengisi mandiri dan judul dokumen otomatis menggunakan Nama Template.') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 pl-4 shrink-0">
                            <span class="hidden sm:inline-block text-xs font-semibold text-primary-600 dark:text-primary-400"
                                x-text="showAdvancedDefaults ? '{{ __('Tutup Pengaturan') }}' : '{{ __('Buka Pengaturan') }}'">
                            </span>
                            <button type="button" class="rounded-lg p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-transform duration-200"
                                :class="showAdvancedDefaults ? 'rotate-180' : ''">
                                <x-icon name="chevron-down" class="w-5 h-5" />
                            </button>
                        </div>
                    </div>

                    <div x-show="showAdvancedDefaults" x-transition class="border-t border-gray-100 dark:border-gray-800 p-5 sm:p-6 space-y-6 bg-gray-50/30 dark:bg-gray-900/20">
                        {{-- Nilai Bawaan Umum --}}
                        <div class="space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                <x-icon name="document-text" class="w-4 h-4 text-gray-400" />
                                {{ __('Nilai Bawaan Umum (Form Pemohon)') }}
                            </h3>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Judul Dokumen') }}</label>
                                    <input type="text" name="fields[title]" value="{{ $fields['title'] ?? '' }}"
                                        placeholder="{{ __('Otomatis menggunakan Nama Template jika dikosongkan') }}"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20 text-sm">
                                    <p class="mt-1 text-xs text-gray-400">{{ __('Teks yang langsung terisi di kotak judul saat pemohon membuat dokumen.') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Prioritas') }}</label>
                                    <select name="fields[priority]"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20 text-sm">
                                        <option value="">— {{ __('Tidak diisi (Normal)') }} —</option>
                                        <option value="low" {{ ($fields['priority'] ?? '') === 'low' ? 'selected' : '' }}>{{ __('Rendah') }}</option>
                                        <option value="normal" {{ ($fields['priority'] ?? '') === 'normal' ? 'selected' : '' }}>{{ __('Normal') }}</option>
                                        <option value="high" {{ ($fields['priority'] ?? '') === 'high' ? 'selected' : '' }}>{{ __('Tinggi') }}</option>
                                        <option value="urgent" {{ ($fields['priority'] ?? '') === 'urgent' ? 'selected' : '' }}>{{ __('Urgent') }}</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Deskripsi / Keterangan') }}</label>
                                    <textarea name="fields[description]" rows="3"
                                        placeholder="{{ __('Teks pengantar atau boilerplate yang otomatis terisi di kolom keterangan pemohon...') }}"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20 text-sm">{{ $fields['description'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Default Tipe: Approval Only --}}
                        <div x-show="approvalType === 'only'" class="pt-4 border-t border-gray-200 dark:border-gray-700/80 space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                <x-icon name="document-check" class="w-4 h-4 text-blue-500" />
                                {{ __('Klasifikasi Dokumen Administratif') }}
                            </h3>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Kategori Dokumen') }}</label>
                                    <input type="text" name="fields[document_category]" list="category_options"
                                        value="{{ $fields['document_category'] ?? '' }}"
                                        placeholder="{{ __('Contoh: Surat Tugas, Memo Internal, SOP') }}"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20 text-sm">
                                    <datalist id="category_options">
                                        <option value="Memo Internal">
                                        <option value="SOP / Kebijakan">
                                        <option value="Surat Tugas">
                                        <option value="Surat Keputusan">
                                    </datalist>
                                    <p class="mt-1 text-xs text-gray-400">{{ __('Kelompok arsip dokumen. Boleh disamakan dengan Nama Template jika ragu.') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Departemen Terkait') }}</label>
                                    <select name="fields[related_department]"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20 text-sm">
                                        <option value="">— {{ __('Tidak diisi') }} —</option>
                                        @foreach(\App\Models\Department::allActive() as $dept)
                                            <option value="{{ $dept->name }}" {{ ($fields['related_department'] ?? '') === $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Default Tipe: PO --}}
                        <div x-show="approvalType === 'po'" class="pt-4 border-t border-gray-200 dark:border-gray-700/80 space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                <x-icon name="shopping-cart" class="w-4 h-4 text-green-500" />
                                {{ __('Default Values — Purchase Order') }}
                            </h3>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Nama Vendor') }}</label>
                                    <input type="text" name="fields[vendor_name]" value="{{ $fields['vendor_name'] ?? '' }}"
                                        placeholder="{{ __('contoh: PT Amazon Web Services') }}"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default NPWP Vendor') }}</label>
                                    <input type="text" name="fields[vendor_npwp]" value="{{ $fields['vendor_npwp'] ?? '' }}"
                                        placeholder="XX.XXX.XXX.X-XXX.XXX"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20 text-sm">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Rincian Barang/Jasa') }}</label>
                                    <textarea name="fields[item_description]" rows="3"
                                        placeholder="1. Cloud Server x 1 unit @ Rp 5.000.000&#10;2. Domain & SSL x 1 paket @ Rp 500.000"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20 text-sm">{{ $fields['item_description'] ?? '' }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Kode Anggaran') }}</label>
                                    <input type="text" name="fields[budget_code]" value="{{ $fields['budget_code'] ?? '' }}"
                                        placeholder="{{ __('contoh: IT-2026-CLOUD') }}"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20 text-sm">
                                </div>
                            </div>
                        </div>

                        {{-- Default Tipe: Invoice --}}
                        <div x-show="approvalType === 'invoice'" class="pt-4 border-t border-gray-200 dark:border-gray-700/80 space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                <x-icon name="receipt-percent" class="w-4 h-4 text-orange-500" />
                                {{ __('Default Values — Invoice') }}
                            </h3>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Nama Vendor') }}</label>
                                    <input type="text" name="fields[vendor_name_invoice]" value="{{ $fields['vendor_name_invoice'] ?? '' }}"
                                        placeholder="{{ __('contoh: PT Telkom Indonesia') }}"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Rekening Tujuan') }}</label>
                                    <input type="text" name="fields[bank_account]" value="{{ $fields['bank_account'] ?? '' }}"
                                        placeholder="BCA - 1234567890 a.n. PT Vendor"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default No. Faktur Pajak') }}</label>
                                    <input type="text" name="fields[tax_invoice]" value="{{ $fields['tax_invoice'] ?? '' }}"
                                        placeholder="010.000-26.00000001"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20 text-sm">
                                </div>
                            </div>
                        </div>

                        {{-- Default Tipe: Loan --}}
                        <div x-show="approvalType === 'loan'" class="pt-4 border-t border-gray-200 dark:border-gray-700/80 space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                <x-icon name="currency-dollar" class="w-4 h-4 text-purple-500" />
                                {{ __('Default Values — Pinjaman') }}
                            </h3>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Default Jenis Pinjaman') }}</label>
                                    <select name="fields[loan_type]"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20 text-sm">
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
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20 text-sm">
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
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20 text-sm">{{ $fields['loan_reason'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Syarat & Ketentuan (Opsional untuk PDF) --}}
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700/80 space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Syarat & Ketentuan Dokumen (Opsional)') }}</label>
                            <textarea name="fields[terms_and_conditions]" rows="3"
                                placeholder="{{ __('Ketikkan syarat dan ketentuan untuk dokumen ini. Setiap baris (enter) akan otomatis dijadikan penomoran list (1, 2, 3) di PDF.') }}"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20 text-sm">{{ old('fields.terms_and_conditions', $fields['terms_and_conditions'] ?? '') }}</textarea>
                            <p class="text-xs text-gray-500">{{ __('Kosongkan jika template ini tidak membutuhkan Syarat & Ketentuan. Gunakan enter untuk memisahkan antar nomor.') }}</p>
                        </div>
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
                            <div><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ __('Identitas') }}</p><p class="mt-0.5 text-[10px] leading-4 text-gray-500">{{ __('Nama, kode, jenis approval, dan layout PDF') }}</p></div>
                        </div>
                        <div class="flex items-start gap-3 rounded-xl px-3 py-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-[10px] font-bold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">02</span>
                            <div><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ __('Form Isian') }}</p><p class="mt-0.5 text-[10px] leading-4 text-gray-500">{{ __('Input dinamis (Dynamic Fields) pengajuan') }}</p></div>
                        </div>
                        <div class="flex items-start gap-3 rounded-xl px-3 py-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-primary-100 text-[10px] font-bold text-primary-700 dark:bg-primary-900/40 dark:text-primary-300">03</span>
                            <div><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ __('Persetujuan') }}</p><p class="mt-0.5 text-[10px] leading-4 text-gray-500">{{ __('Susunan approver yang wajib tanda tangan') }}</p></div>
                        </div>
                        <div class="flex items-start gap-3 rounded-xl px-3 py-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-[10px] font-bold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">04</span>
                            <div><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ __('Tembusan') }}</p><p class="mt-0.5 text-[10px] leading-4 text-gray-500">{{ __('Pihak mengetahui selesai (Opsional)') }}</p></div>
                        </div>
                        <div class="flex items-start gap-3 rounded-xl px-3 py-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-[10px] font-bold text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">05</span>
                            <div><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ __('Auto-Fill') }}</p><p class="mt-0.5 text-[10px] leading-4 text-gray-500">{{ __('Bantuan nilai bawaan & syarat ketentuan') }}</p></div>
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
