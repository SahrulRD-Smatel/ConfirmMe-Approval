<x-app-layout>
    <x-slot name="title">Buat Pengajuan Baru</x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('documents.index') }}"
                class="w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-gray-800 text-gray-500 hover:text-primary-600 dark:hover:text-primary-400 shadow-soft transition-colors">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Buat Pengajuan Baru</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Pilih modul dokumen dan lengkapi form pengajuan Anda (Bisa lebih dari satu sekaligus)
                </p>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto w-full max-w-7xl" x-data="{
        step: {{ old('documents') ? 2 : 1 }},
        searchQuery: '',
        selectedTemplate: null,
        approvalType: '{{ old('documents.0.approval_type') }}',
        isLoading: false,
        // Inisialisasi form repeater dari Laravel old() jika ada error validasi
        forms: [
            @if(old('documents'))
                @foreach(old('documents') as $index => $oldDoc)
                    { id: {{ $index }}, title: '{{ addslashes($oldDoc['title'] ?? '') }}', description: '{{ addslashes($oldDoc['description'] ?? '') }}', subtotal: {{ $oldDoc['subtotal'] ?? 0 }}, taxAmount: {{ $oldDoc['taxAmount'] ?? 0 }} }{{ !$loop->last ? ',' : '' }}
                @endforeach
            @else
                { id: Date.now(), title: '', description: '', subtotal: 0, taxAmount: 0 }
            @endif
        ],
        templates: [
            @foreach($templates as $tpl)
            {
                code: '{{ $tpl->code }}',
                name: '{{ addslashes($tpl->name) }}',
                description: '{{ addslashes($tpl->description ?? '') }}',
                type: '{{ $tpl->approval_type }}',
                category: '{{ $tpl->approval_type === 'only' ? 'Administratif' : ($tpl->approval_type === 'loan' ? 'HR & Pinjaman' : 'Keuangan & PO') }}',
                icon: '{{ $tpl->approval_type === 'only' ? 'document-text' : ($tpl->approval_type === 'loan' ? 'user-group' : 'banknotes') }}',
                color: '{{ $tpl->approval_type === 'only' ? 'blue' : ($tpl->approval_type === 'loan' ? 'pink' : 'emerald') }}',
                defaultTitle: '{{ addslashes($tpl->fields['title'] ?? '') }}',
                defaultDesc: '{{ addslashes($tpl->fields['description'] ?? '') }}',
                defaultCategory: '{{ addslashes($tpl->fields['document_category'] ?? '') }}',
                dynamicFields: {{ Js::from($tpl->fields['dynamic_fields'] ?? []) }}
            },
            @endforeach
        ],
        init() {
            // Restore selected template visually if validation failed
            if (this.approvalType) {
                const oldTplCode = '{{ old('documents.0.template') }}';
                if (oldTplCode) {
                    this.selectedTemplate = this.templates.find(t => t.code === oldTplCode);
                }
            }
        },
        filteredTemplates() {
            if (this.searchQuery === '') return this.templates;
            const q = this.searchQuery.toLowerCase();
            return this.templates.filter(t => t.name.toLowerCase().includes(q) || t.description.toLowerCase().includes(q) || t.category.toLowerCase().includes(q));
        },
        hasCategory(cat) {
            return this.filteredTemplates().some(t => t.category === cat);
        },
        getTemplatesByCategory(cat) {
            return this.filteredTemplates().filter(t => t.category === cat);
        },
        selectTemplate(tpl) {
            this.selectedTemplate = tpl;
            this.approvalType = tpl.type;
            
            // Set defaults to form 0
            this.forms = [{ 
                id: Date.now(), 
                title: tpl.defaultTitle || '', 
                description: tpl.defaultDesc || '',
                subtotal: 0,
                taxAmount: 0
            }];
            
            this.step = 2;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        addForm() {
            if (this.forms.length < 10) {
                this.forms.push({
                    id: Date.now(),
                    title: this.selectedTemplate ? this.selectedTemplate.defaultTitle : '',
                    description: this.selectedTemplate ? this.selectedTemplate.defaultDesc : '',
                    subtotal: 0,
                    taxAmount: 0
                });
            } else {
                window.showAlert('Maksimal 10 dokumen sekaligus.', 'warning');
            }
        },
        removeForm(index) {
            if (this.forms.length > 1) {
                this.forms.splice(index, 1);
            }
        },
        resetSelection() {
            this.step = 1;
            this.selectedTemplate = null;
            this.approvalType = '';
            this.forms = [{ id: Date.now(), title: '', description: '', subtotal: 0, taxAmount: 0 }];
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }">

        <!-- Progress / orientation rail -->
        <section class="mb-8 overflow-hidden rounded-[2rem] border border-gray-200/80 bg-white/90 shadow-soft-lg backdrop-blur-xl dark:border-gray-700/80 dark:bg-gray-900/90">
            <div class="flex flex-col gap-6 p-6 sm:p-7 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-xl">
                    <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-primary-200/80 bg-primary-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-primary-700 dark:border-primary-800/70 dark:bg-primary-900/30 dark:text-primary-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-primary-500"></span>
                        Pengajuan Baru
                    </div>
                    <h2 class="font-display text-2xl font-semibold tracking-tight text-gray-950 dark:text-white sm:text-3xl">Satu alur untuk semua kebutuhan.</h2>
                    <p class="mt-2 max-w-lg text-sm leading-6 text-gray-500 dark:text-gray-400">Pilih modul yang sesuai, lalu lengkapi detailnya dalam satu ruang kerja yang terarah.</p>
                </div>
                <div class="w-full max-w-md lg:min-w-[22rem]">
                    <div class="mb-3 flex items-center justify-between text-[11px] font-bold uppercase tracking-[0.16em] text-gray-500 dark:text-gray-400">
                        <span>Progress pengajuan</span>
                        <span class="text-primary-600 dark:text-primary-400" x-text="step === 1 ? '01 / 02' : '02 / 02'"></span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex min-w-0 flex-1 items-center gap-2">
                            <span :class="step === 1 ? 'bg-primary-600 text-white shadow-soft' : 'bg-primary-100 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300'" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-xs font-bold transition-colors">01</span>
                            <span class="hidden text-xs font-semibold text-gray-700 dark:text-gray-300 sm:block">Pilih modul</span>
                        </div>
                        <div :class="step === 2 ? 'bg-primary-500' : 'bg-gray-200 dark:bg-gray-700'" class="h-1 min-w-8 flex-1 rounded-full transition-colors"></div>
                        <div class="flex min-w-0 flex-1 items-center justify-end gap-2">
                            <span class="hidden text-xs font-semibold text-gray-700 dark:text-gray-300 sm:block">Lengkapi detail</span>
                            <span :class="step === 2 ? 'bg-primary-600 text-white shadow-soft' : 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500'" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-xs font-bold transition-colors">02</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- STEP 1: Pilih Template -->
        <div x-show="step === 1" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100" class="space-y-8">

            <div class="mx-auto max-w-4xl">
                <div class="mb-4 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-400">Langkah pertama</p>
                        <h3 class="mt-1 text-xl font-semibold tracking-tight text-gray-900 dark:text-white sm:text-2xl">Pilih modul pengajuan</h3>
                    </div>
                    <span class="hidden rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-500 dark:bg-gray-800 dark:text-gray-400 sm:inline-flex">{{ count($templates) }} modul tersedia</span>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="relative mx-auto w-full max-w-4xl rounded-[1.75rem] border border-gray-200/80 bg-white/90 p-1.5 shadow-soft-lg dark:border-gray-700/80 dark:bg-gray-900/90">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <x-icon name="magnifying-glass" class="w-6 h-6 text-gray-400" />
                </div>
                <input type="text" x-model="searchQuery"
                    class="block w-full rounded-2xl border-0 bg-white py-4 pl-12 pr-4 text-lg text-gray-900 shadow-soft ring-1 ring-inset ring-gray-200 transition-shadow focus:ring-4 focus:ring-inset focus:ring-primary-500/20 dark:bg-gray-900 dark:text-white dark:ring-gray-700"
                    placeholder="Cari modul pengajuan (misal: Cuti, Dinas, Pengadaan)...">
            </div>

            <!-- Categories -->
            <template x-for="category in ['Administratif', 'Keuangan & PO', 'HR & Pinjaman']" :key="category">
                <details x-show="hasCategory(category)" open class="group/category overflow-hidden rounded-2xl border border-gray-200/80 bg-white/70 shadow-sm dark:border-gray-700/80 dark:bg-gray-900/60">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-4 py-3.5 [&::-webkit-details-marker]:hidden">
                        <div class="flex items-center gap-3">
                            <span class="h-px w-8 bg-primary-300 dark:bg-primary-700"></span>
                            <h3 class="text-xs font-bold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400" x-text="category"></h3>
                        </div>
                        <svg class="h-4 w-4 text-gray-400 transition-transform duration-200 group-open/category:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="m6 9 6 6 6-6" />
                        </svg>
                    </summary>
                    
                    <div class="grid grid-cols-1 gap-3 p-3 pt-0 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                        <template x-for="tpl in getTemplatesByCategory(category)" :key="tpl.code">
                            <div @click="selectTemplate(tpl)"
                                 class="group relative flex min-h-[172px] cursor-pointer flex-col overflow-hidden rounded-2xl border border-gray-200/80 bg-white/90 p-4 shadow-soft backdrop-blur-xl transition-all duration-300 hover:-translate-y-0.5 hover:shadow-soft-lg dark:border-gray-700/70 dark:bg-gray-900/90">
                                 
                                <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-white/0 to-current opacity-5 rounded-bl-[100px]" :class="`text-${tpl.color}-500`"></div>
                                
                                <div class="relative z-10 flex min-h-[64px] items-start gap-3">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform duration-300"
                                         :class="`bg-${tpl.color}-100 dark:bg-${tpl.color}-900/30 text-${tpl.color}-600 dark:text-${tpl.color}-400`">
                                        <template x-if="tpl.icon === 'document-text'"><x-icon name="document-text" class="w-6 h-6" /></template>
                                        <template x-if="tpl.icon === 'user-group'"><x-icon name="user-group" class="w-6 h-6" /></template>
                                        <template x-if="tpl.icon === 'banknotes'"><x-icon name="banknotes" class="w-6 h-6" /></template>
                                        <template x-if="tpl.icon === 'shopping-bag'"><x-icon name="shopping-bag" class="w-6 h-6" /></template>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="mb-2 flex items-start justify-between gap-3">
                                            <h4 class="min-h-[3rem] text-base font-bold leading-6 text-gray-900 transition-colors group-hover:text-primary-600 dark:text-white dark:group-hover:text-primary-400" x-text="tpl.name"></h4>
                                            <x-icon name="arrow-up-right" class="mt-0.5 h-4 w-4 shrink-0 text-gray-300 transition-transform group-hover:-translate-y-0.5 group-hover:translate-x-0.5 group-hover:text-primary-500" />
                                        </div>
                                        <p class="mt-1 min-h-[2.5rem] line-clamp-2 text-xs leading-5 text-gray-500 dark:text-gray-400" x-text="tpl.description || 'Tidak ada deskripsi.'"></p>
                                    </div>
                                </div>
                                <div class="mt-auto flex items-center justify-between border-t border-gray-100 pt-3 text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400 dark:border-gray-800 dark:text-gray-500">
                                    <span x-text="tpl.category"></span>
                                    <span class="text-primary-600 transition-colors group-hover:text-primary-700 dark:text-primary-400">Pilih modul</span>
                                </div>
                            </div>
                        </template>
                    </div>
                </details>
            </template>
        </div>

        <!-- STEP 2: Isi Form (BULK) -->
        <div x-show="step === 2" style="display: none;" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0">

            <div class="mb-6 flex flex-col gap-2">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-400">Langkah kedua</p>
                <h3 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white sm:text-2xl">Lengkapi detail pengajuan</h3>
                <p class="max-w-2xl text-sm leading-6 text-gray-500 dark:text-gray-400">Tinjau modul terpilih, lengkapi setiap dokumen, lalu kirim seluruh pengajuan dari satu ruang kerja.</p>
            </div>

            <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-7">
                @csrf

                <div class="mb-6 rounded-[1.75rem] border border-primary-200/80 bg-primary-50/80 p-5 shadow-sm dark:border-primary-800/70 dark:bg-primary-900/20">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-primary-600 text-white shadow-soft">
                                <x-icon name="sparkles" class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-primary-600 dark:text-primary-300">Modul aktif</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedTemplate ? selectedTemplate.name : 'Melanjutkan form tersimpan'"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 rounded-xl border border-primary-200/80 bg-white/70 px-3 py-2 text-xs font-semibold text-primary-700 dark:border-primary-800/70 dark:bg-gray-900/40 dark:text-primary-300">
                            <span class="h-2 w-2 rounded-full bg-primary-500"></span>
                            <span x-text="`${forms.length} dokumen dalam pengajuan`"></span>
                        </div>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-xl p-4">
                        <div class="flex gap-3">
                            <x-icon name="exclamation-triangle" class="w-5 h-5 text-danger-500 shrink-0 mt-0.5" />
                            <div>
                                <h3 class="text-sm font-bold text-danger-800 dark:text-danger-200">Terjadi kesalahan pada validasi (Beberapa isian wajib diperbaiki)</h3>
                                <ul class="mt-2 text-sm text-danger-700 dark:text-danger-300 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <p class="mt-2 text-sm text-danger-600 dark:text-danger-400 font-medium">Catatan: Harap unggah ulang file lampiran Anda (jika ada) demi keamanan.</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Selected Module Indicator -->
                <div class="flex flex-col gap-4 rounded-[1.75rem] border border-primary-200/80 bg-primary-50/70 p-5 shadow-sm dark:border-primary-800/70 dark:bg-primary-900/20 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center text-primary-600 dark:text-primary-400">
                            <x-icon name="sparkles" class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-xs text-primary-600 dark:text-primary-400 font-semibold uppercase tracking-wider">Modul Terpilih (Bulk Request)</p>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white" x-text="selectedTemplate ? selectedTemplate.name : 'Melanjutkan Form'"></h3>
                        </div>
                    </div>
                    <button type="button" @click="resetSelection()" class="text-sm font-medium text-primary-600 hover:text-primary-700 bg-white/50 dark:bg-gray-800/50 hover:bg-white dark:hover:bg-gray-800/80 px-3 py-1.5 rounded-xl transition-colors border border-primary-200 dark:border-primary-700/50">
                        Ganti Modul
                    </button>
                </div>

                <!-- FORM REPEATER -->
                <div class="space-y-7">
                    <template x-for="(form, index) in forms" :key="form.id">
                        <div class="overflow-hidden rounded-3xl border border-gray-200/80 bg-white shadow-soft transition-all duration-300 dark:border-gray-700/80 dark:bg-gray-900">
                            
                            <!-- Repeater Header -->
                            <div class="bg-gray-50/80 dark:bg-gray-900/50 px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700">
                                <h3 class="font-bold text-gray-800 dark:text-gray-200 flex items-center gap-3">
                                    <span class="bg-primary-500 text-white w-7 h-7 rounded-full flex items-center justify-center text-sm shadow-soft" x-text="index + 1"></span>
                                    <span>Dokumen #<span x-text="index + 1"></span></span>
                                </h3>
                                <button type="button" @click="removeForm(index)" x-show="forms.length > 1" 
                                    class="text-danger-500 hover:text-danger-700 hover:bg-danger-50 dark:hover:bg-danger-900/30 px-3 py-1.5 rounded-xl text-sm font-medium flex items-center gap-1.5 transition-colors">
                                    <x-icon name="trash" class="w-4 h-4"/> Hapus
                                </button>
                            </div>

                            <div class="space-y-9 p-5 sm:p-7">
                                <!-- Hidden bindings -->
                                <input type="hidden" :name="`documents[${index}][approval_type]`" x-model="approvalType">
                                <input type="hidden" :name="`documents[${index}][template]`" x-bind:value="selectedTemplate ? selectedTemplate.code : ''">

                                <!-- BASIC INFO -->
                                <div class="rounded-2xl border border-gray-100 bg-gray-50/60 p-4 dark:border-gray-800 dark:bg-gray-800/40 sm:p-5">
                                    <div class="mb-5 flex items-center gap-3">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-100 text-xs font-bold text-primary-700 dark:bg-primary-900/50 dark:text-primary-300">01</span>
                                        <div>
                                            <h4 class="text-sm font-bold uppercase tracking-[0.14em] text-gray-700 dark:text-gray-200">Informasi Utama</h4>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Identitas dan konteks utama dokumen.</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Dokumen <span class="text-danger-500">*</span></label>
                                            <input type="text" :name="`documents[${index}][title]`" required x-model="form.title"
                                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prioritas <span class="text-danger-500">*</span></label>
                                            <select :name="`documents[${index}][priority]`" required class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20">
                                                <option value="low">Rendah</option>
                                                <option value="normal" selected>Normal</option>
                                                <option value="high">Tinggi</option>
                                                <option value="urgent">Urgent</option>
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi <span class="text-danger-500">*</span></label>
                                            <textarea :name="`documents[${index}][description]`" rows="3" required x-model="form.description"
                                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- PO SPECIFIC -->
                                <div x-show="approvalType === 'po'" class="rounded-2xl border border-gray-100 bg-gray-50/60 p-4 dark:border-gray-800 dark:bg-gray-800/40 sm:p-5"
                                     x-data="{
                                        subtotal: form.subtotal || 0,
                                        taxAmount: form.taxAmount || 0,
                                        grandTotal: 0,
                                        init() { this.calc(); },
                                        calc() {
                                            this.taxAmount = Math.round(this.subtotal * 0.11);
                                            this.grandTotal = parseInt(this.subtotal || 0) + parseInt(this.taxAmount || 0);
                                        },
                                        fmt(n) { return new Intl.NumberFormat('id-ID').format(n); }
                                     }">
                                    <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Detail Purchase Order</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Vendor <span class="text-danger-500">*</span></label>
                                            <input type="text" :name="`documents[${index}][vendor_name]`" class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-white focus:ring-4 focus:ring-primary-500/20">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori Pembelian <span class="text-danger-500">*</span></label>
                                            <select :name="`documents[${index}][po_category]`" class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-white focus:ring-4 focus:ring-primary-500/20">
                                                <option value="internal">Internal Use</option>
                                                <option value="external">External / Client</option>
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rincian Barang/Jasa <span class="text-danger-500">*</span></label>
                                            <textarea :name="`documents[${index}][item_description]`" rows="2" class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-white focus:ring-4 focus:ring-primary-500/20"></textarea>
                                        </div>
                                        <div class="md:col-span-2 bg-gray-50/80 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal (DPP) <span class="text-danger-500">*</span></label>
                                                    <input type="number" :name="`documents[${index}][subtotal]`" x-model="subtotal" @input="calc" min="0" class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-white">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">PPN 11%</label>
                                                    <input type="number" :name="`documents[${index}][tax_amount]`" x-model="taxAmount" @input="grandTotal = parseInt(subtotal||0) + parseInt(taxAmount||0)" class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-100" readonly>
                                                </div>
                                            </div>
                                            <div class="mt-4 flex justify-between items-center text-lg font-bold text-primary-700">
                                                <span>Grand Total</span>
                                                <span x-text="'Rp ' + fmt(grandTotal)">Rp 0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- OTHER TYPES (Simplified for Bulk UI demonstration, typically same as single form) -->
                                <div x-show="approvalType === 'only'" class="rounded-2xl border border-gray-100 bg-gray-50/60 p-4 dark:border-gray-800 dark:bg-gray-800/40 sm:p-5">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Dokumen</label>
                                            <input type="text" :name="`documents[${index}][document_category]`" readonly :value="selectedTemplate ? selectedTemplate.defaultCategory : ''" class="block w-full px-4 py-3 border border-gray-200 bg-gray-50 rounded-xl text-gray-500 cursor-not-allowed">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berlaku</label>
                                            <input type="date" :name="`documents[${index}][effective_date]`" class="block w-full px-4 py-3 border border-gray-300 rounded-xl">
                                        </div>
                                    </div>
                                </div>
                                <!-- DYNAMIC FIELDS -->
                                <div x-show="selectedTemplate && selectedTemplate.dynamicFields && selectedTemplate.dynamicFields.length > 0" class="rounded-2xl border border-gray-100 bg-gray-50/60 p-4 dark:border-gray-800 dark:bg-gray-800/40 sm:p-5">
                                    <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Informasi Spesifik</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <template x-for="field in (selectedTemplate?.dynamicFields || [])" :key="field.name">
                                            <div :class="['textarea', 'table'].includes(field.type) ? 'md:col-span-2' : ''">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    <span x-text="field.label"></span>
                                                    <span x-show="field.required == '1'" class="text-danger-500">*</span>
                                                </label>
                                                
                                                <!-- Text / Number / Date / Time -->
                                                <template x-if="['text', 'number', 'date', 'time'].includes(field.type)">
                                                    <input :type="field.type" :name="`documents[${index}][metadata][${field.name}]`" :required="field.required == '1'"
                                                        class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20">
                                                </template>

                                                <!-- Textarea -->
                                                <template x-if="field.type === 'textarea'">
                                                    <textarea :name="`documents[${index}][metadata][${field.name}]`" rows="3" :required="field.required == '1'"
                                                        class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20"></textarea>
                                                </template>

                                                <!-- Select -->
                                                <template x-if="field.type === 'select'">
                                                    <select :name="`documents[${index}][metadata][${field.name}]`" :required="field.required == '1'"
                                                        class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20">
                                                        <option value="">-- Pilih --</option>
                                                        <template x-for="opt in (field.options ? field.options.split(',').map(s=>s.trim()) : [])">
                                                            <option :value="opt" x-text="opt"></option>
                                                        </template>
                                                    </select>
                                                </template>

                                                <!-- File -->
                                                <template x-if="field.type === 'file'">
                                                    <input type="file" :name="`documents[${index}][metadata_files][${field.name}]`" :required="field.required == '1'"
                                                        class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                                </template>

                                                <!-- Table -->
                                                <template x-if="field.type === 'table'">
                                                    <div class="bg-gray-50/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden"
                                                         x-data="{
                                                            rows: [{}],
                                                            addRow() { this.rows.push({}); },
                                                            removeRow(rIndex) { if(this.rows.length > 1) this.rows.splice(rIndex, 1); },
                                                            getCols(colsStr) { return colsStr ? colsStr.split(',').map(s=>s.trim()) : []; }
                                                         }">
                                                        <div class="overflow-x-auto">
                                                            <table class="min-w-[680px] w-full text-left text-sm">
                                                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                                                    <tr>
                                                                        <template x-for="col in getCols(field.columns)">
                                                                            <th class="px-4 py-3" x-text="col"></th>
                                                                        </template>
                                                                        <th class="px-4 py-3 w-16 text-center">Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <template x-for="(row, rIndex) in rows">
                                                                        <tr class="border-b dark:border-gray-700">
                                                                            <template x-for="col in getCols(field.columns)">
                                                                                <td class="p-2">
                                                                                    <input type="text" :name="`documents[${index}][metadata][${field.name}][${rIndex}][${col}]`" :placeholder="col" :required="field.required == '1'"
                                                                                        class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                                                </td>
                                                                            </template>
                                                                            <td class="p-2 text-center">
                                                                                <button type="button" @click="removeRow(rIndex)" class="text-danger-500 hover:text-danger-700" title="Hapus Baris">
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
                                                                <x-icon name="plus" class="w-3 h-3" /> Tambah Baris
                                                            </button>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- ATTACHMENTS -->
                                <div class="rounded-2xl border border-gray-100 bg-gray-50/60 p-4 dark:border-gray-800 dark:bg-gray-800/40 sm:p-5" x-data="{ files: [] }">
                                    <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Lampiran Tambahan <span class="lowercase normal-case font-medium">(Opsional)</span></h4>
                                    <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl cursor-pointer bg-gray-50/80 dark:bg-gray-800/50 hover:bg-gray-100 transition-colors">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <x-icon name="cloud-arrow-up" class="w-6 h-6 text-gray-400 mb-1" />
                                            <p class="text-sm text-gray-500"><span class="font-semibold text-primary-600">Pilih File Tambahan</span> (Jika ada) untuk Dokumen #<span x-text="index+1"></span></p>
                                        </div>
                                        <input :name="`attachments[${index}][]`" type="file" multiple class="hidden" @change="files = Array.from($event.target.files)" />
                                    </label>
                                    <ul class="mt-3 space-y-2" x-show="files.length > 0">
                                        <template x-for="(file, fi) in files" :key="fi">
                                            <li class="text-xs text-primary-700 bg-primary-50 p-2 rounded-xl" x-text="file.name"></li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- ADD ANOTHER DOC BUTTON -->
                <div class="pt-4 flex justify-center">
                    <button type="button" @click="addForm()" x-show="forms.length < 10" 
                        class="group flex flex-col items-center gap-2 p-6 rounded-3xl border-2 border-dashed border-primary-200 dark:border-primary-800 hover:border-primary-400 dark:hover:border-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 w-full max-w-lg transition-all duration-300">
                        <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <x-icon name="plus" class="w-6 h-6"/>
                        </div>
                        <div>
                            <span class="block font-bold text-primary-700 dark:text-primary-400 text-lg">Tambah Pengajuan Lain</span>
                            <span class="block text-sm text-primary-500/70" x-text="`${forms.length} dari maksimal 10 form`"></span>
                        </div>
                    </button>
                    
                    <div x-show="forms.length >= 10" class="text-center p-6 text-gray-500">
                        Batas maksimal 10 pengajuan tercapai.
                    </div>
                </div>

                <!-- SUBMIT ACTIONS -->
                <div class="sticky bottom-5 z-40 mt-8 flex flex-col gap-3 rounded-[1.75rem] border border-gray-200/80 bg-white/95 p-4 shadow-soft-lg shadow-primary-500/10 backdrop-blur-xl dark:border-gray-700/80 dark:bg-gray-900/95 sm:flex-row sm:items-center sm:justify-end">
                    <x-ui.button type="button" variant="secondary" @click="resetSelection()">
                        Batal
                    </x-ui.button>
                    <x-ui.button type="submit" icon="paper-airplane" class="shadow-lg shadow-primary-500/30 font-bold px-8">
                        Ajukan <span x-text="forms.length"></span> Dokumen Sekaligus
                    </x-ui.button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>