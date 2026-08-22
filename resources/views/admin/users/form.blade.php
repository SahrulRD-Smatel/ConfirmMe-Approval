<x-app-layout>
    <x-slot name="title">{{ isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna' }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    {{ isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna' }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Atur identitas, akses, dan detail direktori pengguna.</p>
            </div>
        </div>
    </x-slot>

    <form action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST"
        class="mx-auto w-full max-w-6xl">
        @csrf
        @if(isset($user)) @method('PUT') @endif

        <x-ui.card class="space-y-7">
        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,1.18fr)_minmax(18rem,0.82fr)]">
            <div class="space-y-6">
                <section class="overflow-hidden rounded-[1.75rem] border border-gray-200/80 bg-white/95 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 dark:bg-gray-900/95">
                    <div class="border-b border-gray-200/80 px-6 py-5 dark:border-gray-800/80">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-primary-50 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400">
                                <x-icon name="identification" class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-400">01 / Identity</p>
                                <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Identitas pengguna</h2>
                                <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">Informasi utama yang digunakan untuk mengenali akun di seluruh sistem.</p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">Nama <span class="text-danger-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
                                class="block h-12 w-full rounded-xl border border-gray-200 bg-gray-50/80 px-4 text-sm text-gray-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-800/80 dark:text-white">
                            @error('name')<p class="mt-2 text-sm text-danger-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">Email <span class="text-danger-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                                class="block h-12 w-full rounded-xl border border-gray-200 bg-gray-50/80 px-4 text-sm text-gray-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-800/80 dark:text-white">
                            @error('email')<p class="mt-2 text-sm text-danger-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">Password {{ isset($user) ? '(kosongkan jika tidak diubah)' : '' }}</label>
                                <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">Secure credential</span>
                            </div>
                            <input type="password" name="password" {{ isset($user) ? '' : 'required' }}
                                class="block h-12 w-full rounded-xl border border-gray-200 bg-gray-50/80 px-4 text-sm text-gray-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-800/80 dark:text-white">
                            @error('password')<p class="mt-2 text-sm text-danger-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-[1.75rem] border border-gray-200/80 bg-white/95 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 dark:bg-gray-900/95">
                    <div class="border-b border-gray-200/80 px-6 py-5 dark:border-gray-800/80">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                <x-icon name="identification" class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">03 / Directory</p>
                                <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Detail direktori</h2>
                                <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">Informasi tambahan untuk pencarian dan konteks organisasi.</p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">Posisi/Jabatan</label>
                            <input type="text" name="position" value="{{ old('position', $user->position ?? '') }}"
                                class="block h-12 w-full rounded-xl border border-gray-200 bg-gray-50/80 px-4 text-sm text-gray-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-800/80 dark:text-white">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">ID Karyawan</label>
                            <input type="text" name="employee_id" value="{{ old('employee_id', $user->employee_id ?? '') }}"
                                class="block h-12 w-full rounded-xl border border-gray-200 bg-gray-50/80 px-4 text-sm text-gray-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-800/80 dark:text-white">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                                class="block h-12 w-full rounded-xl border border-gray-200 bg-gray-50/80 px-4 text-sm text-gray-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-800/80 dark:text-white">
                        </div>
                    </div>
                </section>
            </div>

            <div class="space-y-6 lg:sticky lg:top-6">
                <section class="overflow-hidden rounded-[1.75rem] border border-primary-200/80 bg-primary-50/70 shadow-soft-lg shadow-primary-900/[0.04] dark:border-primary-800/70 dark:bg-primary-900/20">
                    <div class="border-b border-primary-200/70 px-6 py-5 dark:border-primary-800/60">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-primary-600 text-white shadow-soft">
                                <x-icon name="shield-check" class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-300">02 / Access</p>
                                <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Akses & penempatan</h2>
                                <p class="mt-1 text-xs leading-5 text-gray-600 dark:text-gray-400">Role dan departemen menentukan pengalaman serta cakupan akses pengguna.</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-5 p-6">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">Role <span class="text-danger-500">*</span></label>
                            <select name="role" required
                                class="block h-12 w-full rounded-xl border border-primary-200 bg-white px-4 text-sm text-gray-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-primary-800 dark:bg-gray-900 dark:text-white">
                                <option value="">Pilih Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role', isset($user) ? $user->roles->first()?->name : '') === $role->name ? 'selected' : '' }}>
                                        {{ role_label($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')<p class="mt-2 text-sm text-danger-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">Departemen</label>
                            <select name="department"
                                class="block h-12 w-full rounded-xl border border-primary-200 bg-white px-4 text-sm text-gray-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-primary-800 dark:bg-gray-900 dark:text-white">
                                <option value="">Pilih Departemen</option>
                                @foreach(\App\Models\Department::allActive() as $dept)
                                    <option value="{{ $dept->name }}" {{ old('department', $user->department ?? '') === $dept->name ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department')<p class="mt-2 text-sm text-danger-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <div class="rounded-[1.5rem] border border-gray-200/80 bg-gray-50/80 p-5 dark:border-gray-800/80 dark:bg-gray-800/50">
                    <div class="flex gap-3">
                        <x-icon name="information-circle" class="mt-0.5 h-5 w-5 shrink-0 text-primary-600 dark:text-primary-400" />
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Sebelum menyimpan</p>
                            <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">Pastikan role dan departemen sudah sesuai dengan tanggung jawab pengguna.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </x-ui.card>

        <div class="sticky bottom-5 z-30 mt-6 flex flex-col gap-3 rounded-[1.5rem] border border-gray-200/80 bg-white/95 p-4 shadow-soft-lg backdrop-blur-xl dark:border-gray-800/80 dark:bg-gray-900/95 sm:flex-row sm:items-center sm:justify-between">
            <p class="hidden text-xs text-gray-400 dark:text-gray-500 sm:block">Perubahan akan diterapkan setelah form disimpan.</p>
            <div class="flex items-center justify-end gap-3">
                <x-ui.button type="button" variant="secondary"
                    href="{{ route('admin.users.index') }}">Batal</x-ui.button>
                <x-ui.button type="submit" icon="check">Simpan</x-ui.button>
            </div>
        </div>
    </form>
</x-app-layout>