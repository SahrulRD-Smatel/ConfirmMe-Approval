<x-app-layout>
    <x-slot name="title">{{ __('Konfigurasi Workflow & Aturan Mesin') }}</x-slot>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Konfigurasi Workflow & Aturan Mesin') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Kelola logika hierarki persetujuan (auto-skip), peran fungsional, dan izin tipe pengajuan per departemen.') }}</p>
            </div>
            <form action="{{ route('admin.workflow.reset') }}" method="POST" onsubmit="confirmAction(event, '{{ __('Reset semua konfigurasi workflow dan hierarki ke default bawaan sistem?') }}')">
                @csrf
                <input type="hidden" name="type" value="all">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-xs">
                    <x-icon name="arrow-path" class="w-4 h-4 text-gray-500" />
                    {{ __('Reset ke Default') }}
                </button>
            </form>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Contextual Bridge Banner --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800 shadow-xs">
            <div class="flex items-start gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                    <x-icon name="cog-6-tooth" class="h-5 w-5" />
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Logika Otomatisasi & Rantai Persetujuan') }}</h2>
                    <p class="mt-0.5 text-xs text-gray-600 dark:text-gray-300">
                        {{ __('Halaman ini mengatur logika mesin approval (tingkat jabatan untuk auto-skip & peran fungsional). Jika Anda ingin menentukan urutan siapa saja yang menandatangani dokumen tertentu (Step 1 -> Step 2), silakan kelola di') }}
                        <a href="{{ route('admin.templates.index') }}" class="font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400 underline">{{ __('Template Dokumen') }} &rarr;</a>
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.templates.index') }}" class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 shadow-xs dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                <x-icon name="document-text" class="h-4 w-4 text-gray-500 dark:text-gray-400" />
                {{ __('Template Dokumen') }}
            </a>
        </div>

        {{-- Tabs --}}
        <div x-data="{ activeTab: 'settings' }" class="space-y-6">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex gap-4 -mb-px">
                    <button @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:hover:text-gray-300'"
                        class="flex items-center gap-2 px-4 py-3 text-sm font-semibold border-b-2 transition-colors">
                        <x-icon name="adjustments-horizontal" class="w-4 h-4" />
                        {{ __('Hierarki & Aturan Approval') }}
                    </button>
                    <button @click="activeTab = 'access'" :class="activeTab === 'access' ? 'border-primary-600 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:hover:text-gray-300'"
                        class="flex items-center gap-2 px-4 py-3 text-sm font-semibold border-b-2 transition-colors">
                        <x-icon name="building-office-2" class="w-4 h-4" />
                        {{ __('Izin Tipe per Departemen') }}
                    </button>
                </nav>
            </div>

            {{-- TAB 1: Settings (Role Hierarchy, Functional Roles, HRD Keywords) --}}
            <div x-show="activeTab === 'settings'" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    {{-- LEFT COLUMN: Role Hierarchy Ladder (Auto-Skip Engine) --}}
                    <div class="lg:col-span-7 space-y-6">
                        <section class="rounded-xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-gray-800 shadow-xs">
                            <div class="flex items-start justify-between gap-4 border-b border-gray-100 dark:border-gray-700 pb-4 mb-4">
                                <div>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                        {{ __('Approval Engine') }} • {{ __('Auto-Skip') }}
                                    </span>
                                    <h2 class="mt-2 text-lg font-bold text-gray-900 dark:text-white">{{ __('Tingkatan Hierarki Jabatan') }}</h2>
                                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Tingkat jabatan menentukan atasan langsung. Level lebih tinggi = jabatan lebih tinggi.') }}</p>
                                </div>
                            </div>

                            {{-- Explainer Box --}}
                            <div class="mb-5 flex items-start gap-2.5 rounded-lg border border-gray-200 bg-gray-50 p-3.5 text-xs text-gray-700 dark:border-gray-700 dark:bg-gray-900/50 dark:text-gray-300">
                                <x-icon name="information-circle" class="w-4 h-4 text-gray-500 shrink-0 mt-0.5" />
                                <div class="leading-relaxed">
                                    <span class="font-bold text-gray-900 dark:text-white">{{ __('Prinsip Auto-Skip:') }}</span>
                                    {{ __('Jika pengaju dokumen memiliki Level 3 (Manager), sistem secara otomatis melewati approver berlevel ≤ 3 (seperti Supervisor Level 2) di dalam alur persetujuan, dan langsung menuju ke atasan di atasnya.') }}
                                </div>
                            </div>

                            <form action="{{ route('admin.workflow.settings.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="section" value="role_hierarchy">
                                
                                <div class="space-y-2 max-h-[460px] overflow-y-auto pr-1">
                                    @php
                                        $sortedHierarchy = collect($roleHierarchy)->sortDesc();
                                    @endphp
                                    @foreach($sortedHierarchy as $role => $level)
                                    <div class="flex items-center justify-between gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 shrink-0">
                                                Lvl {{ $level }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                                    {{ $roleLabels[$role] ?? role_label($role) }}
                                                </p>
                                                <p class="text-[11px] font-mono text-gray-400 truncate">{{ $role }}</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Level:') }}</span>
                                            <input type="number" name="hierarchy[{{ $role }}]" value="{{ $level }}" min="1" max="10"
                                                class="w-16 text-center text-sm font-bold border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-primary-500 focus:border-primary-500 shadow-xs">
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <button type="submit" class="mt-5 w-full px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition shadow-xs">
                                    {{ __('Simpan Perubahan Hierarki') }}
                                </button>
                            </form>
                        </section>
                    </div>

                    {{-- RIGHT COLUMN: Functional Roles & HR Keywords --}}
                    <div class="lg:col-span-5 space-y-6">
                        
                        {{-- Functional Roles Card --}}
                        <section class="rounded-xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-gray-800 shadow-xs">
                            <div class="border-b border-gray-100 dark:border-gray-700 pb-4 mb-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                    <x-icon name="shield-check" class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" />
                                    {{ __('Pengecualian Auto-Skip') }}
                                </span>
                                <h2 class="mt-2 text-base font-bold text-gray-900 dark:text-white">{{ __('Peran Fungsional (Kebal Skip)') }}</h2>
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Role yang dicentang TIDAK akan dilewati oleh auto-skip, meski pengaju berjabatan lebih tinggi.') }}</p>
                            </div>

                            <div class="mb-4 flex items-start gap-2.5 rounded-lg border border-gray-200 bg-gray-50 p-3 text-xs text-gray-700 dark:border-gray-700 dark:bg-gray-900/50 dark:text-gray-300">
                                <x-icon name="information-circle" class="w-4 h-4 text-gray-500 shrink-0 mt-0.5" />
                                <div class="leading-relaxed">
                                    <span class="font-bold text-gray-900 dark:text-white">{{ __('Pemeriksaan Wajib:') }}</span>
                                    {{ __('Role seperti Finance/Purchasing/HRD memeriksa anggaran & legalitas, sehingga selalu dilibatkan dalam rantai persetujuan dokumen terkait.') }}
                                </div>
                            </div>

                            <form action="{{ route('admin.workflow.settings.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="section" value="functional_roles">
                                
                                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                                    @foreach($allRoles as $r)
                                    @if(!in_array($r, ['admin','staff']))
                                    @php $isFunctional = in_array($r, $functionalRoles); @endphp
                                    <label class="flex items-center justify-between p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" name="functional_roles[]" value="{{ $r }}" {{ $isFunctional ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                            <div>
                                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $roleLabels[$r] ?? role_label($r) }}</span>
                                                <span class="block text-[10px] font-mono text-gray-400">{{ $r }}</span>
                                            </div>
                                        </div>
                                        @if($isFunctional)
                                            <span class="text-[10px] font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded border border-gray-200 dark:border-gray-600">{{ __('Kebal Skip') }}</span>
                                        @endif
                                    </label>
                                    @endif
                                    @endforeach
                                </div>

                                <button type="submit" class="mt-4 w-full px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition shadow-xs">
                                    {{ __('Simpan Peran Fungsional') }}
                                </button>
                            </form>
                        </section>

                        {{-- HRD Keywords Card --}}
                        <section class="rounded-xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-gray-800 shadow-xs">
                            <div class="border-b border-gray-100 dark:border-gray-700 pb-3 mb-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                    <x-icon name="tag" class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" />
                                    {{ __('Deteksi Cerdas') }}
                                </span>
                                <h2 class="mt-2 text-base font-bold text-gray-900 dark:text-white">{{ __('Kata Kunci Dokumen HRD') }}</h2>
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Mendeteksi dokumen terkait kepegawaian pada tipe Approval Only.') }}</p>
                            </div>

                            <form action="{{ route('admin.workflow.settings.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="section" value="hrd_keywords">
                                
                                <div class="mb-3 flex flex-wrap gap-1 max-h-24 overflow-y-auto p-1.5 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                                    @foreach($hrdKeywords as $kw)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-white text-gray-700 border border-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
                                            #{{ $kw }}
                                        </span>
                                    @endforeach
                                </div>

                                <textarea name="keywords" rows="3" class="w-full text-xs border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-primary-500 focus:border-primary-500 shadow-xs" placeholder="{{ __('Pisahkan setiap kata kunci dengan tanda koma (,)') }}">{{ implode(', ', $hrdKeywords) }}</textarea>
                                
                                <button type="submit" class="mt-3 w-full px-4 py-2 text-xs font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition shadow-xs">
                                    {{ __('Simpan Kata Kunci HRD') }}
                                </button>
                            </form>
                        </section>

                    </div>
                </div>
            </div>

            {{-- TAB 2: Type Access per Department --}}
            <div x-show="activeTab === 'access'">
                <div class="rounded-xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-gray-800 shadow-xs">
                    <div class="border-b border-gray-100 dark:border-gray-700 pb-4 mb-5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                            {{ __('Matriks Hak Akses') }}
                        </span>
                        <h2 class="mt-2 text-lg font-bold text-gray-900 dark:text-white">{{ __('Akses Tipe Approval per Departemen') }}</h2>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Centang departemen yang diizinkan membuat setiap tipe approval. Centang kolom "Semua" untuk mengizinkan seluruh departemen.') }}</p>
                    </div>

                    <form action="{{ route('admin.workflow.type-access.update') }}" method="POST">
                        @csrf
                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">{{ __('Tipe Approval') }}</th>
                                        <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700">{{ __('Semua Dept') }}</th>
                                        @foreach($departments as $dept)
                                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400">{{ $dept }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @php
                                        $typeMeta = [
                                            'only' => ['label' => 'Approval Only', 'desc' => 'Persetujuan umum non-anggaran'],
                                            'po' => ['label' => 'PO External', 'desc' => 'Pembelian pihak ketiga/vendor'],
                                            'po_internal' => ['label' => 'PO Internal', 'desc' => 'Kebutuhan internal antar divisi'],
                                            'invoice' => ['label' => 'Invoice', 'desc' => 'Pembayaran tagihan lanjutan PO'],
                                            'loan' => ['label' => 'Pinjaman', 'desc' => 'Pengajuan pinjaman karyawan'],
                                        ];
                                    @endphp
                                    @foreach($typeMeta as $type => $info)
                                    @php
                                        $currentAccess = $typeAccess[$type] ?? [];
                                        $isAll = in_array('*', $currentAccess);
                                    @endphp
                                    <tr x-data="{ allDepts: {{ $isAll ? 'true' : 'false' }} }" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-4 py-3">
                                            <span class="font-semibold text-gray-900 dark:text-white block">{{ $info['label'] }}</span>
                                            <span class="text-xs text-gray-400 block">{{ $info['desc'] }}</span>
                                        </td>
                                        <td class="px-3 py-3 text-center bg-gray-50 dark:bg-gray-800">
                                            <input type="checkbox" x-model="allDepts" name="type_access[{{ $type }}][]" value="*" {{ $isAll ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 h-4 w-4">
                                        </td>
                                        @foreach($departments as $dept)
                                        <td class="px-3 py-3 text-center">
                                            <input type="checkbox" name="type_access[{{ $type }}][]" value="{{ $dept }}"
                                                {{ in_array($dept, $currentAccess) ? 'checked' : '' }}
                                                :disabled="allDepts"
                                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 disabled:opacity-20 h-4 w-4">
                                        </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-5 flex justify-end">
                            <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition shadow-xs">
                                {{ __('Simpan Pengaturan Akses') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>