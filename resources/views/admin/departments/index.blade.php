<x-app-layout>
    <x-slot name="title">Manajemen Departemen</x-slot>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="card-elevated p-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-48 h-48 bg-gradient-to-bl from-primary-50/60 to-transparent rounded-full -mr-16 -mt-16 pointer-events-none"></div>
            <div class="relative z-10">
                <h1 class="text-xl font-bold text-gray-900">Manajemen Departemen</h1>
                <p class="text-sm text-gray-500">Kelola departemen perusahaan. Perubahan nama otomatis update ke semua user terkait.</p>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <x-icon name="check-circle" class="w-5 h-5 text-green-500" />
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <x-icon name="exclamation-circle" class="w-5 h-5 text-red-500" />
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- Add Department Form --}}
            <div class="lg:col-span-1">
                <div class="card-elevated p-6">
                    <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <x-icon name="plus-circle" class="w-5 h-5 text-primary-500" />
                        Tambah Departemen
                    </h2>
                    <form action="{{ route('admin.departments.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Departemen <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="contoh: Marketing"
                                class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-4 focus:ring-primary-500/20/10 focus:border-primary-500">
                            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode <span class="text-red-500">*</span></label>
                            <input type="text" name="code" value="{{ old('code') }}" required placeholder="contoh: MKT" maxlength="20"
                                class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-4 focus:ring-primary-500/20/10 focus:border-primary-500 uppercase">
                            @error('code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Direktur Role</label>
                            <select name="director_role"
                                class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-4 focus:ring-primary-500/20/10 focus:border-primary-500">
                                <option value="">— Tidak ada —</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ old('director_role') === $role ? 'selected' : '' }}>
                                        {{ role_label($role) }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-400">Role yang menjadi direktur/kepala departemen ini</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-4 focus:ring-primary-500/20/10 focus:border-primary-500">
                        </div>
                        <button type="submit"
                            class="w-full px-4 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-xl hover:bg-primary-700 transition flex items-center justify-center gap-2">
                            <x-icon name="plus" class="w-4 h-4" />
                            Tambah Departemen
                        </button>
                    </form>
                </div>
            </div>

            {{-- Department List --}}
            <div class="lg:col-span-2">
                <div class="card-elevated overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <x-icon name="building-office" class="w-5 h-5 text-primary-500" />
                            Daftar Departemen
                            <span class="ml-auto text-xs font-medium text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full">
                                {{ $departments->count() }} departemen
                            </span>
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($departments as $dept)
                            <div class="px-6 py-4 hover:bg-primary-50/60/50 transition" x-data="{ editing: false }">
                                {{-- View Mode --}}
                                <div x-show="!editing" class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-50 to-primary-100 flex items-center justify-center">
                                            <span class="text-xs font-bold text-primary-700">{{ $dept->code }}</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 text-sm">{{ $dept->name }}</p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                @if($dept->director_role)
                                                    <span class="text-xs text-gray-500">
                                                        Direktur: <span class="font-medium text-primary-600">{{ role_label($dept->director_role) }}</span>
                                                    </span>
                                                @else
                                                    <span class="text-xs text-gray-400 italic">Tanpa direktur</span>
                                                @endif
                                                <span class="text-gray-300">•</span>
                                                <span class="text-xs text-gray-400">
                                                    {{ \App\Models\User::where('department', $dept->name)->count() }} user
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if(!$dept->is_active)
                                            <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-0.5 rounded-full">Nonaktif</span>
                                        @endif
                                        <button @click="editing = true"
                                            class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition">
                                            <x-icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                        @if(\App\Models\User::where('department', $dept->name)->count() === 0)
                                            <form action="{{ route('admin.departments.destroy', $dept) }}" method="POST"
                                                onsubmit="confirmAction(event, 'Hapus departemen {{ $dept->name }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition">
                                                    <x-icon name="trash" class="w-4 h-4" />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                {{-- Edit Mode --}}
                                <form x-show="editing" x-cloak action="{{ route('admin.departments.update', $dept) }}" method="POST"
                                    class="space-y-3">
                                    @csrf @method('PUT')
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Nama</label>
                                            <input type="text" name="name" value="{{ $dept->name }}" required
                                                class="w-full px-2.5 py-1.5 border border-gray-300 rounded-xl text-sm focus:ring-4 focus:ring-primary-500/20/10">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Kode</label>
                                            <input type="text" name="code" value="{{ $dept->code }}" required maxlength="20"
                                                class="w-full px-2.5 py-1.5 border border-gray-300 rounded-xl text-sm focus:ring-4 focus:ring-primary-500/20/10 uppercase">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Direktur Role</label>
                                            <select name="director_role"
                                                class="w-full px-2.5 py-1.5 border border-gray-300 rounded-xl text-sm focus:ring-4 focus:ring-primary-500/20/10">
                                                <option value="">— Tidak ada —</option>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role }}" {{ $dept->director_role === $role ? 'selected' : '' }}>{{ role_label($role) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Urutan</label>
                                            <input type="number" name="sort_order" value="{{ $dept->sort_order }}" min="0"
                                                class="w-full px-2.5 py-1.5 border border-gray-300 rounded-xl text-sm focus:ring-4 focus:ring-primary-500/20/10">
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="flex items-center gap-2 text-sm text-gray-600">
                                            <input type="hidden" name="is_active" value="0">
                                            <input type="checkbox" name="is_active" value="1" {{ $dept->is_active ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500/20">
                                            Aktif
                                        </label>
                                        <div class="ml-auto flex items-center gap-2">
                                            <button type="button" @click="editing = false"
                                                class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-xl transition">
                                                Batal
                                            </button>
                                            <button type="submit"
                                                class="px-3 py-1.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-xl transition">
                                                Simpan
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @empty
                            <div class="px-6 py-12 text-center">
                                <x-icon name="building-office" class="w-10 h-10 text-gray-300 mx-auto mb-2" />
                                <p class="text-sm text-gray-400">Belum ada departemen</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
