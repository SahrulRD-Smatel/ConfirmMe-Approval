<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Profile') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola identitas, keamanan, dan informasi organisasi Anda dari satu ruang kerja.</p>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto w-full max-w-6xl space-y-6">
        <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-[minmax(0,0.82fr)_minmax(0,1.18fr)]">
            {{-- Profile Photo Card --}}
            <x-ui.card class="overflow-hidden !p-0">
                <div class="border-b border-gray-200/80 bg-gradient-to-br from-primary-50 via-white to-white px-6 py-6 dark:border-gray-800/80 dark:from-primary-900/20 dark:via-gray-900 dark:to-gray-900">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-primary-600 text-white shadow-soft">
                            <x-icon name="camera" class="h-5 w-5" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-400">Profile identity</p>
                            <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Foto profil</h2>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-profile-photo-form')
                </div>
            </x-ui.card>

            {{-- Role & Division Info Card --}}
            <x-ui.card class="overflow-hidden !p-0">
                <div class="border-b border-gray-200/80 px-6 py-6 dark:border-gray-800/80">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-400">Organization context</p>
                            <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Informasi Jabatan') }}</h2>
                            <p class="mt-1 max-w-xl text-xs leading-5 text-gray-500 dark:text-gray-400">{{ __('Informasi jabatan dan divisi Anda dalam organisasi.') }}</p>
                        </div>
                        <div class="hidden h-10 w-10 items-center justify-center rounded-2xl bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-300 sm:flex">
                            <x-icon name="building-office" class="h-5 w-5" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 p-6 sm:grid-cols-2">
                    {{-- Role Badge --}}
                    <div class="rounded-2xl border border-gray-200/80 bg-gray-50/70 p-4 dark:border-gray-800/80 dark:bg-gray-800/60 sm:col-span-2">
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">Role</span>
                            <x-icon name="shield-check" class="h-4 w-4 text-primary-500" />
                        </div>
                        @php
                            $user = Auth::user();
                            $roles = $user->getRoleNames();
                            $primaryRole = $roles->first() ?? 'user';

                            $roleColors = [
                                'admin' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                'presiden_direktur' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                                'direktur_hr' => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300',
                                'direktur_finance' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300',
                                'direktur_it' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                'direktur_sales' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                                'manager' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
                                'supervisor' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-300',
                                'hrd' => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300',
                                'finance' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300',
                                'staff' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                            ];

                            $roleLabels = [
                                'admin' => 'Administrator',
                                'presiden_direktur' => 'Presiden Direktur',
                                'direktur_hr' => 'Direktur HR',
                                'direktur_finance' => 'Direktur Finance',
                                'direktur_it' => 'Direktur IT',
                                'direktur_sales' => 'Direktur Sales',
                                'manager' => 'Manager',
                                'supervisor' => 'Supervisor',
                                'hrd' => 'HRD',
                                'finance' => 'Finance',
                                'staff' => 'Staff',
                            ];

                            $badgeColor = $roleColors[$primaryRole] ?? 'bg-gray-100 text-gray-800';
                            $roleLabel = $roleLabels[$primaryRole] ?? ucfirst($primaryRole);
                        @endphp
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold {{ $badgeColor }}">
                            {{ $roleLabel }}
                        </span>
                    </div>

                    {{-- Position --}}
                    <div class="rounded-2xl border border-gray-200/80 bg-gray-50/70 p-4 dark:border-gray-800/80 dark:bg-gray-800/60">
                        <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">Jabatan</span>
                        <span class="mt-2 block truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $user->position ?? '-' }}</span>
                    </div>

                    {{-- Department --}}
                    <div class="rounded-2xl border border-gray-200/80 bg-gray-50/70 p-4 dark:border-gray-800/80 dark:bg-gray-800/60">
                        <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">Departemen</span>
                        <span class="mt-2 block truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $user->department ?? '-' }}</span>
                    </div>

                    {{-- Division --}}
                    <div class="rounded-2xl border border-gray-200/80 bg-gray-50/70 p-4 dark:border-gray-800/80 dark:bg-gray-800/60">
                        <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">Divisi</span>
                        @php
                            $divisionColors = [
                                'HR' => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300',
                                'Finance' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300',
                                'IT' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                'Sales' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                            ];
                            $divColor = $divisionColors[$user->division] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                        @endphp
                        @if($user->division)
                            <span class="mt-2 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $divColor }}">
                                {{ $user->division }}
                            </span>
                        @else
                            <span class="mt-2 block text-sm text-gray-500 dark:text-gray-400">-</span>
                        @endif
                    </div>

                    {{-- Employee ID --}}
                    <div class="rounded-2xl border border-gray-200/80 bg-gray-50/70 p-4 dark:border-gray-800/80 dark:bg-gray-800/60">
                        <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400">Employee ID</span>
                        <span class="mt-2 block truncate font-mono text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $user->employee_id ?? '-' }}</span>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-2">
            <x-ui.card class="overflow-hidden !p-0">
                <div class="border-b border-gray-200/80 px-6 py-5 dark:border-gray-800/80">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-400">Personal details</p>
                    <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Informasi profil</h2>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </x-ui.card>

            <x-ui.card class="overflow-hidden !p-0">
                <div class="border-b border-gray-200/80 px-6 py-5 dark:border-gray-800/80">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-400">Security</p>
                    <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Keamanan akun</h2>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-password-form')
                </div>
            </x-ui.card>
        </div>

        <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-2">
            <x-ui.card class="overflow-hidden !p-0">
                <div class="border-b border-gray-200/80 px-6 py-5 dark:border-gray-800/80">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-400">Finance profile</p>
                    <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Informasi bank</h2>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-bank-info-form')
                </div>
            </x-ui.card>

            <x-ui.card class="overflow-hidden border-danger-200/80 !p-0 dark:border-danger-800/60">
                <div class="border-b border-danger-200/70 bg-danger-50/60 px-6 py-5 dark:border-danger-800/60 dark:bg-danger-900/10">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-danger-600 dark:text-danger-400">Danger zone</p>
                    <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Hapus akun</h2>
                </div>
                <div class="p-6">
                    @include('profile.partials.delete-user-form')
                </div>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>