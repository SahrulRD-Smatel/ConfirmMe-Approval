<x-app-layout>
    <x-slot name="title">{{ isset($role) ? 'Konfigurasi Hak Akses' : 'Tambah Role Baru' }}</x-slot>
    
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.roles.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-gray-800 text-gray-500 hover:text-primary-600 dark:hover:text-primary-400 shadow-soft transition-colors">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    {{ isset($role) ? 'Konfigurasi Hak Akses' : 'Tambah Role Baru' }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ isset($role) ? 'Atur modul dan fitur apa saja yang dapat diakses oleh '.role_label($role->name) : 'Buat peran baru dan tentukan hak aksesnya.' }}
                </p>
            </div>
        </div>
    </x-slot>

    <form action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST">
        @csrf
        @if(isset($role)) @method('PUT') @endif
        
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 xl:gap-8 relative">
            
            {{-- Left Panel: Role Information --}}
            <div class="lg:col-span-1">
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl border border-gray-200/50 dark:border-gray-700/50 p-6 shadow-soft sticky top-24">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Informasi Role</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama Role <span class="text-danger-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" required
                                   {{ isset($role) && in_array($role->name, ['admin', 'staff', 'supervisor', 'hrd', 'direktur', 'finance', 'purchasing', 'direktur_operasional', 'hrd_coordinator', 'manager', 'direktur_hr', 'direktur_finance', 'direktur_it', 'direktur_sales', 'presiden_direktur']) ? 'readonly' : '' }}
                                   class="block w-full px-4 py-3 border border-gray-200/80 dark:border-gray-700/80 rounded-xl bg-gray-50/80 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10 transition-all font-medium
                                          {{ isset($role) && in_array($role->name, ['admin', 'staff', 'supervisor', 'hrd', 'direktur', 'finance', 'purchasing', 'direktur_operasional', 'hrd_coordinator', 'manager', 'direktur_hr', 'direktur_finance', 'direktur_it', 'direktur_sales', 'presiden_direktur']) ? 'opacity-75 cursor-not-allowed' : '' }}">
                            @error('name')<p class="mt-2 text-sm text-danger-600 font-medium">{{ $message }}</p>@enderror
                            
                            @if(isset($role) && in_array($role->name, ['admin', 'staff', 'supervisor', 'hrd', 'direktur', 'finance', 'purchasing', 'direktur_operasional', 'hrd_coordinator', 'manager', 'direktur_hr', 'direktur_finance', 'direktur_it', 'direktur_sales', 'presiden_direktur']))
                                <p class="text-xs text-amber-600 dark:text-amber-400 mt-2 flex items-start gap-1">
                                    <x-icon name="shield-check" class="w-4 h-4 flex-shrink-0" />
                                    Nama role sistem tidak dapat diubah.
                                </p>
                            @endif
                        </div>
                    </div>
                    
                    <hr class="my-6 border-gray-100 dark:border-gray-700">
                    
                    <div class="space-y-3">
                        <x-ui.button type="submit" icon="check" class="w-full justify-center py-3 text-base shadow-lg shadow-primary-500/30">
                            Simpan Konfigurasi
                        </x-ui.button>
                        <x-ui.button type="button" variant="secondary" href="{{ route('admin.roles.index') }}" class="w-full justify-center py-3 text-base">
                            Batal
                        </x-ui.button>
                    </div>
                </div>
            </div>

            {{-- Right Panel: Permissions Configuration --}}
            <div class="lg:col-span-3 space-y-6">
                @foreach($permGroups as $groupName => $groupPerms)
                <div x-data="{ 
                        groupOpen: true,
                        toggleAll(e) {
                            const checkboxes = this.$root.querySelectorAll('input[type=checkbox]:not(.group-toggle)');
                            checkboxes.forEach(cb => cb.checked = e.target.checked);
                        }
                     }" 
                     class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden shadow-soft">
                    
                    {{-- Group Header --}}
                    <div class="bg-gray-50/80 dark:bg-gray-800/80 px-6 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/50 cursor-pointer" @click="groupOpen = !groupOpen">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white dark:bg-gray-900 flex items-center justify-center shadow-soft text-primary-600">
                                @if($groupName == 'Dokumen') <x-icon name="document-text" class="w-5 h-5" />
                                @elseif($groupName == 'Keuangan') <x-icon name="banknotes" class="w-5 h-5" />
                                @elseif($groupName == 'Laporan') <x-icon name="presentation-chart-bar" class="w-5 h-5" />
                                @elseif($groupName == 'HR & Absensi') <x-icon name="user-group" class="w-5 h-5" />
                                @elseif($groupName == 'Administrasi') <x-icon name="cog-6-tooth" class="w-5 h-5" />
                                @else <x-icon name="squares-2x2" class="w-5 h-5" /> @endif
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white uppercase tracking-wide text-sm">{{ $groupName }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Konfigurasi hak akses modul {{ strtolower($groupName) }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4" @click.stop>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <span class="text-xs font-semibold text-gray-500 group-hover:text-primary-600 transition-colors">PILIH SEMUA</span>
                                <div class="relative inline-flex items-center">
                                    <input type="checkbox" class="sr-only peer group-toggle" @change="toggleAll">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                                </div>
                            </label>
                            
                            <button type="button" class="text-gray-400 hover:text-gray-600" @click="groupOpen = !groupOpen">
                                <x-icon name="chevron-down" class="w-5 h-5 transition-transform duration-300" x-bind:class="groupOpen ? 'rotate-180' : ''" />
                            </button>
                        </div>
                    </div>
                    
                    {{-- Group Body --}}
                    <div x-show="groupOpen" x-collapse>
                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-y-4 gap-x-8">
                            @foreach($groupPerms as $perm)
                                @php 
                                    $hasPerm = isset($role) ? $role->hasPermissionTo($perm) : false; 
                                    $isAction = in_array($perm, ['edit-document', 'delete-document', 'submit-document', 'reject-document', 'print-document', 'view-all-documents', 'view-department-documents', 'view-finance-documents', 'export-reports']);
                                    $badgeLabel = $isAction ? 'FITUR' : 'MENU';
                                    $badgeClass = $isAction ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400';
                                @endphp
                                <label class="flex items-center justify-between group cursor-pointer p-3 rounded-xl hover:bg-primary-50/60 dark:hover:bg-gray-800/80/50 transition-colors border border-transparent hover:border-gray-100 dark:hover:border-gray-700/50">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors flex items-center gap-2">
                                        {{ str_replace('-', ' ', $perm) }}
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                    </span>
                                    <div class="relative inline-flex items-center">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm }}" class="sr-only peer" {{ $hasPerm ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-500"></div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
        </div>
    </form>
</x-app-layout>
