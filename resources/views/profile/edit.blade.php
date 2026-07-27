<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Profile Photo Card --}}
            <x-ui.card>
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-photo-form')
                </div>
            </x-ui.card>

            {{-- Role & Division Info Card --}}
            <x-ui.card>
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Informasi Jabatan') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Informasi jabatan dan divisi Anda dalam organisasi.') }}
                        </p>
                    </header>

                    <div class="mt-6 space-y-4">
                        {{-- Role Badge --}}
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Role</span>
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
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $badgeColor }}">
                                {{ $roleLabel }}
                            </span>
                        </div>

                        {{-- Position --}}
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Jabatan</span>
                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $user->position ?? '-' }}</span>
                        </div>

                        {{-- Department --}}
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Departemen</span>
                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $user->department ?? '-' }}</span>
                        </div>

                        {{-- Division --}}
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Divisi</span>
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
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $divColor }}">
                                    {{ $user->division }}
                                </span>
                            @else
                                <span class="text-sm text-gray-500 dark:text-gray-400">-</span>
                            @endif
                        </div>

                        {{-- Employee ID --}}
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Employee ID</span>
                            <span
                                class="text-sm font-mono text-gray-900 dark:text-gray-100">{{ $user->employee_id ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="max-w-xl">
                    @include('profile.partials.update-bank-info-form')
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>