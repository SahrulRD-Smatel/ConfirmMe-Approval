<x-app-layout>
    <x-slot name="title">Konfigurasi Workflow</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Konfigurasi Workflow</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola alur approval, threshold, dan akses secara dinamis</p>
            </div>
            <form action="{{ route('admin.workflow.reset') }}" method="POST" onsubmit="confirmAction(event, 'Reset semua konfigurasi ke default?')">
                @csrf
                <input type="hidden" name="type" value="all">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-primary-50/60 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Reset ke Default
                </button>
            </form>
        </div>
    </x-slot>

    {{-- Tabs --}}
    <div x-data="{ activeTab: 'access' }" class="space-y-6">
        <div class="border-b border-gray-200/80 dark:border-gray-700/80">
            <nav class="flex gap-4 -mb-px">
                @foreach(['access' => 'Akses Tipe', 'settings' => 'Pengaturan'] as $tab => $label)
                <button @click="activeTab = '{{ $tab }}'" :class="activeTab === '{{ $tab }}' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors">{{ $label }}</button>
                @endforeach
            </nav>
        </div>


        {{-- TAB 3: Type Access --}}
        <div x-show="activeTab === 'access'" x-transition>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft border border-gray-200/80 dark:border-gray-700/80 p-6">
                <h3 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Akses Tipe Approval per Departemen</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Centang departemen yang diizinkan membuat setiap tipe approval. Centang "Semua" untuk mengizinkan semua departemen.</p>
                <form action="{{ route('admin.workflow.type-access.update') }}" method="POST">
                    @csrf
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200/80 dark:border-gray-700/80">
                                    <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400">Tipe Approval</th>
                                    <th class="px-3 py-2 text-center text-gray-500 dark:text-gray-400">Semua</th>
                                    @foreach($departments as $dept)
                                    <th class="px-3 py-2 text-center text-gray-500 dark:text-gray-400">{{ $dept }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200/80 dark:divide-gray-700/80">
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
                        <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-xl">Simpan Pengaturan Akses</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- TAB 4: Settings --}}
        <div x-show="activeTab === 'settings'" x-transition>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                {{-- Role Hierarchy --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft border border-gray-200/80 dark:border-gray-700/80 p-6">
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
                        <button type="submit" class="mt-4 w-full px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-xl">Simpan Hierarchy</button>
                    </form>
                </div>

                {{-- Functional Roles --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft border border-gray-200/80 dark:border-gray-700/80 p-6">
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
                        <button type="submit" class="mt-4 w-full px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-xl">Simpan Functional Roles</button>
                    </form>
                </div>

                {{-- HRD Keywords --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft border border-gray-200/80 dark:border-gray-700/80 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">HRD Keywords</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Keyword untuk mendeteksi dokumen HR-related pada Approval Only. Pisahkan dengan koma.</p>
                    <form action="{{ route('admin.workflow.settings.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="section" value="hrd_keywords">
                        <textarea name="keywords" rows="4" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-xl">{{ implode(', ', $hrdKeywords) }}</textarea>
                        <button type="submit" class="mt-3 w-full px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-xl">Simpan Keywords</button>
                    </form>
                </div>


            </div>
        </div>
    </div>
</x-app-layout>