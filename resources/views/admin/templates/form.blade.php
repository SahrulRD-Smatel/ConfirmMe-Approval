<x-app-layout>
    <x-slot name="title">{{ isset($template) ? 'Edit Template' : 'Buat Template' }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.templates.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    {{ isset($template) ? 'Edit Template' : 'Buat Template' }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">Isi default values agar user tidak perlu mengisi ulang setiap kali</p>
            </div>
        </div>
    </x-slot>

    @php
        $fields = old('fields', $template->fields ?? []);
    @endphp

    <form action="{{ isset($template) ? route('admin.templates.update', $template) : route('admin.templates.store') }}"
        method="POST" class="max-w-4xl space-y-6" x-data="{
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
            }
        }"
        @submit="if(workflows.length === 0) { alert('Harap tambahkan minimal 1 approver pada Susunan Approver!'); $event.preventDefault(); }">
        @csrf
        @if(isset($template)) @method('PUT') @endif

        {{-- Basic Info --}}
        <x-ui.card>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <x-icon name="document-text" class="w-5 h-5 text-primary-500" />
                Informasi Template
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Template <span class="text-danger-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $template->name ?? '') }}" required
                        placeholder="contoh: PO Langganan Cloud Bulanan"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    @error('name')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode <span class="text-danger-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $template->code ?? '') }}" required
                        placeholder="contoh: PO-CLOUD" maxlength="50"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10 uppercase">
                    @error('code')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Approval <span class="text-danger-500">*</span></label>
                    <select name="approval_type" required x-model="approvalType"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                        <option value="only">Approval Only (Memo, SOP, Surat Tugas)</option>
                        <option value="po">Approval to PO (Purchase Order)</option>
                        <option value="invoice">Approval to Invoice (Tagihan)</option>
                        <option value="loan">Employee Loan (Pinjaman)</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi Template</label>
                    <textarea name="description" rows="2" placeholder="Deskripsi singkat kapan template ini digunakan..."
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">{{ old('description', $template->description ?? '') }}</textarea>
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $template->is_active ?? true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500/20">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Template Aktif</span>
                    </label>
                </div>
            </div>
        </x-ui.card>

        {{-- Default Values: Common --}}
        <x-ui.card>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 flex items-center gap-2">
                <x-icon name="sparkles" class="w-5 h-5 text-amber-500" />
                Default Values — Umum
            </h2>
            <p class="text-sm text-gray-500 mb-4">Field ini akan otomatis terisi saat user memilih template</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Judul</label>
                    <input type="text" name="fields[title]" value="{{ $fields['title'] ?? '' }}"
                        placeholder="contoh: Pengajuan Pembelian Server Cloud"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    <p class="mt-1 text-xs text-gray-400">Judul yang otomatis terisi, user bisa mengedit</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Prioritas</label>
                    <select name="fields[priority]"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                        <option value="">— Tidak diisi —</option>
                        <option value="low" {{ ($fields['priority'] ?? '') === 'low' ? 'selected' : '' }}>Rendah</option>
                        <option value="normal" {{ ($fields['priority'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="high" {{ ($fields['priority'] ?? '') === 'high' ? 'selected' : '' }}>Tinggi</option>
                        <option value="urgent" {{ ($fields['priority'] ?? '') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Deskripsi</label>
                    <textarea name="fields[description]" rows="3"
                        placeholder="Boilerplate teks yang otomatis terisi..."
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">{{ $fields['description'] ?? '' }}</textarea>
                </div>
            </div>
        </x-ui.card>

        {{-- Default Values: Approval Only --}}
        <div x-show="approvalType === 'only'" x-transition>
            <x-ui.card>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 flex items-center gap-2">
                    <x-icon name="document-check" class="w-5 h-5 text-blue-500" />
                    Default Values — Dokumen Administratif
                </h2>
                <p class="text-sm text-gray-500 mb-4">Khusus untuk tipe "Approval Only"</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Kategori</label>
                        <input type="text" name="fields[document_category]" list="category_options"
                            value="{{ $fields['document_category'] ?? '' }}"
                            placeholder="Contoh: Surat Perjalanan Dinas"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                        <datalist id="category_options">
                            <option value="Memo Internal">
                            <option value="SOP / Kebijakan">
                            <option value="Surat Tugas">
                            <option value="Surat Keputusan">
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Departemen Terkait</label>
                        <select name="fields[related_department]"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                            <option value="">— Tidak diisi —</option>
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
            <x-ui.card>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 flex items-center gap-2">
                    <x-icon name="shopping-cart" class="w-5 h-5 text-green-500" />
                    Default Values — Purchase Order
                </h2>
                <p class="text-sm text-gray-500 mb-4">Khusus untuk tipe "Approval to PO"</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Nama Vendor</label>
                        <input type="text" name="fields[vendor_name]" value="{{ $fields['vendor_name'] ?? '' }}"
                            placeholder="contoh: PT Amazon Web Services"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default NPWP Vendor</label>
                        <input type="text" name="fields[vendor_npwp]" value="{{ $fields['vendor_npwp'] ?? '' }}"
                            placeholder="XX.XXX.XXX.X-XXX.XXX"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Rincian Barang/Jasa</label>
                        <textarea name="fields[item_description]" rows="3"
                            placeholder="1. Cloud Server x 1 unit @ Rp 5.000.000&#10;2. Domain & SSL x 1 paket @ Rp 500.000"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">{{ $fields['item_description'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Kode Anggaran</label>
                        <input type="text" name="fields[budget_code]" value="{{ $fields['budget_code'] ?? '' }}"
                            placeholder="contoh: IT-2026-CLOUD"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Default Values: Invoice --}}
        <div x-show="approvalType === 'invoice'" x-transition>
            <x-ui.card>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 flex items-center gap-2">
                    <x-icon name="receipt-percent" class="w-5 h-5 text-orange-500" />
                    Default Values — Invoice
                </h2>
                <p class="text-sm text-gray-500 mb-4">Khusus untuk tipe "Approval to Invoice"</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Nama Vendor</label>
                        <input type="text" name="fields[vendor_name_invoice]" value="{{ $fields['vendor_name_invoice'] ?? '' }}"
                            placeholder="contoh: PT Telkom Indonesia"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Rekening Tujuan</label>
                        <input type="text" name="fields[bank_account]" value="{{ $fields['bank_account'] ?? '' }}"
                            placeholder="BCA - 1234567890 a.n. PT Vendor"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default No. Faktur Pajak</label>
                        <input type="text" name="fields[tax_invoice]" value="{{ $fields['tax_invoice'] ?? '' }}"
                            placeholder="010.000-26.00000001"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Default Values: Loan --}}
        <div x-show="approvalType === 'loan'" x-transition>
            <x-ui.card>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 flex items-center gap-2">
                    <x-icon name="currency-dollar" class="w-5 h-5 text-purple-500" />
                    Default Values — Pinjaman
                </h2>
                <p class="text-sm text-gray-500 mb-4">Khusus untuk tipe "Employee Loan"</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Jenis Pinjaman</label>
                        <select name="fields[loan_type]"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                            <option value="">— Tidak diisi —</option>
                            <option value="darurat" {{ ($fields['loan_type'] ?? '') === 'darurat' ? 'selected' : '' }}>Pinjaman Darurat</option>
                            <option value="pendidikan" {{ ($fields['loan_type'] ?? '') === 'pendidikan' ? 'selected' : '' }}>Pinjaman Pendidikan</option>
                            <option value="perumahan" {{ ($fields['loan_type'] ?? '') === 'perumahan' ? 'selected' : '' }}>Pinjaman Perumahan</option>
                            <option value="kendaraan" {{ ($fields['loan_type'] ?? '') === 'kendaraan' ? 'selected' : '' }}>Pinjaman Kendaraan</option>
                            <option value="lainnya" {{ ($fields['loan_type'] ?? '') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Cicilan</label>
                        <select name="fields[installment_count]"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                            <option value="">— Tidak diisi —</option>
                            <option value="3" {{ ($fields['installment_count'] ?? '') === '3' ? 'selected' : '' }}>3 Bulan</option>
                            <option value="6" {{ ($fields['installment_count'] ?? '') === '6' ? 'selected' : '' }}>6 Bulan</option>
                            <option value="12" {{ ($fields['installment_count'] ?? '') === '12' ? 'selected' : '' }}>12 Bulan</option>
                            <option value="24" {{ ($fields['installment_count'] ?? '') === '24' ? 'selected' : '' }}>24 Bulan</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Alasan</label>
                        <textarea name="fields[loan_reason]" rows="2"
                            placeholder="Boilerplate alasan pinjaman..."
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">{{ $fields['loan_reason'] ?? '' }}</textarea>
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Workflow / Approver Chain --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="user-group" class="w-5 h-5 text-indigo-500" />
                    Susunan Approver (Wajib)
                </h2>
                <button type="button" @click="addWorkflow"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-xl text-sm font-medium transition-colors">
                    <x-icon name="plus" class="w-4 h-4" /> Tambah Approver
                </button>
            </div>
            
            <p class="text-sm text-gray-500 mb-4">
                Tentukan urutan persetujuan dokumen. Sistem akan memproses persetujuan sesuai urutan dari atas ke bawah.
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
                                <option value="" disabled>-- Pilih Approver --</option>
                                <optgroup label="Berdasarkan Role/Jabatan">
                                    @foreach($roles as $roleData)
                                        <option value="role:{{ $roleData->name }}">{{ role_label($roleData->name) }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Berdasarkan Spesifik User">
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

                        <!-- Remove Button -->
                        <button type="button" @click="removeWorkflow(index)"
                            class="p-2 text-gray-400 hover:text-danger-500 hover:bg-danger-50 rounded-xl transition-colors"
                            title="Hapus">
                            <x-icon name="trash" class="w-5 h-5" />
                        </button>
                    </div>
                </template>
            </div>

            <div x-show="workflows.length === 0" class="text-center py-6 border-2 border-dashed border-gray-200/80 dark:border-gray-700/80 rounded-xl">
                <p class="text-sm text-gray-500">Belum ada approver yang ditambahkan.</p>
                <button type="button" @click="addWorkflow" class="mt-2 text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                    + Tambah Approver Pertama
                </button>
            </div>
        </x-ui.card>

        {{-- Actions --}}
        <div class="flex flex-wrap items-center justify-end gap-3">
            <x-ui.button type="button" variant="secondary" href="{{ route('admin.templates.index') }}">Batal</x-ui.button>
            <x-ui.button type="submit" icon="check">Simpan Template</x-ui.button>
        </div>
    </form>
</x-app-layout>