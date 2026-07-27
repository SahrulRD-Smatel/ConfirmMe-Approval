<x-app-layout>
    <x-slot name="title">Konfigurasi Workflow</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-display">Konfigurasi Workflow</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola alur approval, threshold, dan akses secara dinamis</p>
            </div>
            <form action="{{ route('admin.workflow.reset') }}" method="POST" onsubmit="confirmAction(event, 'Reset semua konfigurasi ke default?')">
                @csrf
                <input type="hidden" name="type" value="all">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Reset ke Default
                </button>
            </form>
        </div>
    </x-slot>

    {{-- Tabs --}}
    <div x-data="{ activeTab: 'chains' }" class="space-y-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex gap-4 -mb-px">
                @foreach(['chains' => 'Approval Chains', 'thresholds' => 'Threshold Nominal', 'access' => 'Akses Tipe', 'settings' => 'Pengaturan'] as $tab => $label)
                <button @click="activeTab = '{{ $tab }}'" :class="activeTab === '{{ $tab }}' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors">{{ $label }}</button>
                @endforeach
            </nav>
        </div>

        {{-- TAB 1: Approval Chains --}}
        <div x-show="activeTab === 'chains'" x-transition>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @php $typeLabels = ['only'=>'Approval Only','po'=>'PO External','po_internal'=>'PO Internal','invoice'=>'Invoice','loan'=>'Pinjaman Karyawan']; @endphp
                @foreach($typeLabels as $type => $label)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ $label }}</h3>
                    <form action="{{ route('admin.workflow.chains.update') }}" method="POST" x-data="{
                        roles: {{ json_encode(isset($workflowTypes[$type]) ? array_values($workflowTypes[$type]) : []) }}
                    }">
                        @csrf
                        <input type="hidden" name="approval_type" value="{{ $type }}">

                        <div class="space-y-2 mb-4">
                            <template x-for="(role, index) in roles" :key="index">
                                <div class="flex items-center gap-2">
                                    <span class="flex-shrink-0 w-7 h-7 rounded-full bg-primary-600 text-white flex items-center justify-center text-xs font-bold" x-text="index + 1"></span>
                                    <select :name="'roles[' + index + ']'" x-model="roles[index]"
                                        class="flex-1 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg">
                                        <option value="">-- Pilih Role --</option>
                                        @foreach($allRoles as $r)
                                        <option value="{{ $r }}">{{ $roleLabels[$r] ?? ucfirst(str_replace('_',' ',$r)) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" @click="roles.splice(index, 1)" class="p-1 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <div class="flex gap-2">
                            <button type="button" @click="roles.push('')" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 text-sm font-medium text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Tambah Step
                            </button>
                            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
                @endforeach
            </div>
        </div>

        {{-- TAB 2: Threshold Nominal --}}
        <div x-show="activeTab === 'thresholds'" x-transition>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Threshold Nominal (Approval Only)</h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400">Label</th>
                                    <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400">Min (Rp)</th>
                                    <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400">Max (Rp)</th>
                                    <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400">Final Role</th>
                                    <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400">Urutan</th>
                                    <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400">Aktif</th>
                                    <th class="px-3 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($thresholds->get('only', collect()) as $t)
                                <tr>
                                    <form action="{{ route('admin.workflow.thresholds.update', $t->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <td class="px-3 py-2"><input type="text" name="label" value="{{ $t->label }}" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded"></td>
                                        <td class="px-3 py-2"><input type="number" name="min_amount" value="{{ (int)$t->min_amount }}" class="w-24 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded"></td>
                                        <td class="px-3 py-2"><input type="number" name="max_amount" value="{{ $t->max_amount ? (int)$t->max_amount : '' }}" placeholder="∞" class="w-24 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded"></td>
                                        <td class="px-3 py-2">
                                            <select name="final_role" class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                                                @foreach($allRoles as $r)
                                                <option value="{{ $r }}" {{ $t->final_role === $r ? 'selected' : '' }}>{{ $roleLabels[$r] ?? ucfirst(str_replace('_',' ',$r)) }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-3 py-2"><input type="number" name="sort_order" value="{{ $t->sort_order }}" class="w-16 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded"></td>
                                        <td class="px-3 py-2">
                                            <input type="hidden" name="is_active" value="0">
                                            <input type="checkbox" name="is_active" value="1" {{ $t->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600">
                                        </td>
                                        <td class="px-3 py-2 flex gap-1">
                                            <button type="submit" class="px-2 py-1 text-xs bg-primary-600 text-white rounded hover:bg-primary-700">Simpan</button>
                                    </form>
                                    <form action="{{ route('admin.workflow.thresholds.destroy', $t->id) }}" method="POST" onsubmit="confirmAction(event, 'Hapus threshold ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
                                    </form>
                                        </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="px-3 py-4 text-center text-gray-500">Belum ada threshold</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Add new threshold --}}
                    <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Tambah Threshold Baru</h4>
                        <form action="{{ route('admin.workflow.thresholds.store') }}" method="POST" class="grid grid-cols-2 md:grid-cols-6 gap-3">
                            @csrf
                            <input type="hidden" name="approval_type" value="only">
                            <input type="text" name="label" placeholder="Label" required class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded col-span-2">
                            <input type="number" name="min_amount" placeholder="Min" required class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                            <input type="number" name="max_amount" placeholder="Max (kosong=∞)" class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                            <select name="final_role" required class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                                <option value="">Final Role</option>
                                @foreach($allRoles as $r)
                                <option value="{{ $r }}">{{ $roleLabels[$r] ?? ucfirst(str_replace('_',' ',$r)) }}</option>
                                @endforeach
                            </select>
                            <div class="flex gap-2">
                                <input type="number" name="sort_order" placeholder="Urutan" value="0" required class="w-20 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                                <button type="submit" class="px-4 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Tambah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 3: Type Access --}}
        <div x-show="activeTab === 'access'" x-transition>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Akses Tipe Approval per Departemen</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Centang departemen yang diizinkan membuat setiap tipe approval. Centang "Semua" untuk mengizinkan semua departemen.</p>
                <form action="{{ route('admin.workflow.type-access.update') }}" method="POST">
                    @csrf
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400">Tipe Approval</th>
                                    <th class="px-3 py-2 text-center text-gray-500 dark:text-gray-400">Semua</th>
                                    @foreach($departments as $dept)
                                    <th class="px-3 py-2 text-center text-gray-500 dark:text-gray-400">{{ $dept }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @php $typeLabelsAccess = ['only'=>'Approval Only','po'=>'PO External','po_internal'=>'PO Internal','invoice'=>'Invoice','loan'=>'Pinjaman']; @endphp
                                @foreach($typeLabelsAccess as $type => $label)
                                @php $currentAccess = $typeAccess[$type] ?? []; $isAll = in_array('*', $currentAccess); @endphp
                                <tr x-data="{ allDepts: {{ $isAll ? 'true' : 'false' }} }">
                                    <td class="px-3 py-3 font-medium text-gray-900 dark:text-white">{{ $label }}</td>
                                    <td class="px-3 py-3 text-center">
                                        <input type="checkbox" x-model="allDepts" name="type_access[{{ $type }}][]" value="*" {{ $isAll ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-primary-600">
                                    </td>
                                    @foreach($departments as $dept)
                                    <td class="px-3 py-3 text-center">
                                        <input type="checkbox" name="type_access[{{ $type }}][]" value="{{ $dept }}"
                                            {{ in_array($dept, $currentAccess) ? 'checked' : '' }}
                                            :disabled="allDepts"
                                            class="rounded border-gray-300 text-primary-600 disabled:opacity-30">
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg">Simpan Pengaturan Akses</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- TAB 4: Settings --}}
        <div x-show="activeTab === 'settings'" x-transition>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Role Hierarchy --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Role Hierarchy</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Level lebih tinggi = jabatan lebih tinggi. Role dengan level ≤ requester akan di-skip otomatis.</p>
                    <form action="{{ route('admin.workflow.settings.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="section" value="role_hierarchy">
                        <div class="space-y-2 max-h-80 overflow-y-auto">
                            @foreach($roleHierarchy as $role => $level)
                            <div class="flex items-center gap-3">
                                <span class="flex-1 text-sm text-gray-700 dark:text-gray-300">{{ $roleLabels[$role] ?? ucfirst(str_replace('_',' ',$role)) }}</span>
                                <input type="number" name="hierarchy[{{ $role }}]" value="{{ $level }}" min="1" max="10"
                                    class="w-16 text-sm text-center border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                            </div>
                            @endforeach
                        </div>
                        <button type="submit" class="mt-4 w-full px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg">Simpan Hierarchy</button>
                    </form>
                </div>

                {{-- Functional Roles --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Functional Roles</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Role fungsional TIDAK akan di-skip oleh auto-skip hierarchy. Centang role yang bersifat fungsional.</p>
                    <form action="{{ route('admin.workflow.settings.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="section" value="functional_roles">
                        <div class="space-y-2">
                            @foreach($allRoles as $r)
                            @if(!in_array($r, ['admin','staff']))
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="functional_roles[]" value="{{ $r }}" {{ in_array($r, $functionalRoles) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-primary-600">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $roleLabels[$r] ?? ucfirst(str_replace('_',' ',$r)) }}</span>
                            </label>
                            @endif
                            @endforeach
                        </div>
                        <button type="submit" class="mt-4 w-full px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg">Simpan Functional Roles</button>
                    </form>
                </div>

                {{-- HRD Keywords --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">HRD Keywords</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Keyword untuk mendeteksi dokumen HR-related pada Approval Only. Pisahkan dengan koma.</p>
                    <form action="{{ route('admin.workflow.settings.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="section" value="hrd_keywords">
                        <textarea name="keywords" rows="4" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg">{{ implode(', ', $hrdKeywords) }}</textarea>
                        <button type="submit" class="mt-3 w-full px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg">Simpan Keywords</button>
                    </form>
                </div>

                {{-- Departments --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Daftar Departemen</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Daftar departemen yang tersedia. Pisahkan dengan koma.</p>
                    <form action="{{ route('admin.workflow.settings.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="section" value="departments">
                        <textarea name="departments" rows="4" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg">{{ implode(', ', $departments) }}</textarea>
                        <button type="submit" class="mt-3 w-full px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg">Simpan Departemen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>