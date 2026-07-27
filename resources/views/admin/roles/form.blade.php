<x-app-layout>
    <x-slot name="title">{{ isset($role) ? 'Edit Role' : 'Tambah Role' }}</x-slot>
    
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.roles.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-display">
                {{ isset($role) ? 'Edit Role: ' . ucfirst($role->name) : 'Tambah Role' }}
            </h1>
        </div>
    </x-slot>

    <form action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST" class="max-w-2xl">
        @csrf
        @if(isset($role)) @method('PUT') @endif
        
        <x-ui.card class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Role <span class="text-danger-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" required
                       {{ isset($role) && in_array($role->name, ['admin', 'staff', 'supervisor', 'hrd', 'direktur', 'finance']) ? 'readonly' : '' }}
                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('name')<p class="mt-1 text-sm text-danger-600">{{ $message }}</p>@enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Permissions</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-64 overflow-y-auto p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    @foreach($permissions as $permission)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                   {{ isset($role) && $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                   class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $permission->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <x-ui.button type="button" variant="secondary" href="{{ route('admin.roles.index') }}">Batal</x-ui.button>
                <x-ui.button type="submit" icon="check">Simpan</x-ui.button>
            </div>
        </x-ui.card>
    </form>
</x-app-layout>
