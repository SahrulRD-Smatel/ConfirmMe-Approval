<x-app-layout>
    <x-slot name="title">{{ __('Dashboard Admin Sistem') }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    {{ __('Administrator Control Panel') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Selamat datang') }}, <span class="font-semibold text-gray-700 dark:text-gray-300">{{ auth()->user()->name }}</span>. {{ __('Anda memiliki akses penuh ke sistem.') }}
                </p>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-medium bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ now()->translatedFormat('l, d M Y') }}
                </span>
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 shadow-sm border border-blue-100 dark:border-blue-800">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    {{ __('System Admin') }}
                </span>
            </div>
        </div>
    </x-slot>

    {{-- SECTION 1: Top Metrics (Pastel Cards) --}}
    <div class="mb-8 grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">
        {{-- Total Users --}}
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-5 relative overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-white/80 text-xs font-bold uppercase tracking-wider mb-1">{{ __('Total Pengguna') }}</p>
                    <h3 class="text-3xl font-bold text-white mb-1">
                        {{ number_format($adminStats['total_users']) }}
                    </h3>
                </div>
                <div class="p-2.5 bg-white/20 rounded-xl shrink-0">
                    <x-icon name="users" class="w-5 h-5 text-white" />
                </div>
            </div>
            <div>
                <span class="inline-flex items-center px-2 py-1 rounded bg-white/20 text-white text-[10px] font-medium">
                    {{ $adminStats['users_growth'] >= 0 ? '+' : '' }}{{ $adminStats['users_growth'] }}
                </span>
                <span class="text-white/80 text-xs ml-1">{{ __('bulan ini') }}</span>
            </div>
        </div>

        {{-- Departments --}}
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-5 relative overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-white/80 text-xs font-bold uppercase tracking-wider mb-1">{{ __('Departemen Aktif') }}</p>
                    <h3 class="text-3xl font-bold text-white mb-1">
                        {{ $adminStats['total_departments'] }}
                    </h3>
                </div>
                <div class="p-2.5 bg-white/20 rounded-xl shrink-0">
                    <x-icon name="building-office-2" class="w-5 h-5 text-white" />
                </div>
            </div>
            <div>
                <span class="text-white/80 text-xs">{{ __('Total unit terdaftar di sistem') }}</span>
            </div>
        </div>

        {{-- System Features --}}
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-5 relative overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-white/80 text-xs font-bold uppercase tracking-wider mb-1">{{ __('Fitur Sistem Aktif') }}</p>
                    <h3 class="text-3xl font-bold text-white mb-1">
                        {{ $adminStats['active_features'] }}/{{ $adminStats['total_features'] }}
                    </h3>
                </div>
                <div class="p-2.5 bg-white/20 rounded-xl shrink-0">
                    <x-icon name="cpu-chip" class="w-5 h-5 text-white" />
                </div>
            </div>
            <div>
                <span class="text-white/80 text-xs">{{ __('Modul sistem yang menyala') }}</span>
            </div>
        </div>

        {{-- System Health / Uptime --}}
        <div class="bg-gradient-to-br from-orange-400 to-orange-500 rounded-2xl p-5 relative overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-white/80 text-xs font-bold uppercase tracking-wider mb-1">{{ __('Status Sistem') }}</p>
                    <h3 class="text-3xl font-bold text-white mb-1">
                        {{ __('Normal') }}
                    </h3>
                </div>
                <div class="p-2.5 bg-white/20 rounded-xl shrink-0">
                    <x-icon name="shield-check" class="w-5 h-5 text-white" />
                </div>
            </div>
            <div>
                <span class="inline-flex items-center text-white/90 text-xs font-medium">
                    <span class="w-2 h-2 rounded-full bg-green-300 animate-pulse mr-1.5"></span>
                    {{ __('Semua layanan operasional') }}
                </span>
            </div>
        </div>
    </div>

    {{-- SECTION 2: Analytics & Traffic --}}
    <div class="mb-8 grid grid-cols-1 gap-5 lg:grid-cols-3">
        {{-- Traffic API / Audit Logs Chart --}}
        <div class="lg:col-span-2">
            <x-ui.card>
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Aktivitas Sistem (7 Hari Terakhir)') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('Total log aktivitas per hari berdasarkan Audit Log') }}</p>
                    </div>
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">{{ __('Live') }}</span>
                </div>
                <div class="h-64">
                    <canvas id="trafficChart"></canvas>
                </div>
            </x-ui.card>
        </div>

        {{-- Role Distribution --}}
        <div>
            <x-ui.card>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">{{ __('Distribusi Role Pengguna') }}</h3>
                <div class="h-64 flex items-center justify-center relative">
                    <canvas id="roleChart"></canvas>
                </div>
            </x-ui.card>
        </div>
    </div>

    {{-- SECTION 3: Recent Users Table --}}
    <x-ui.card :padding="false">
        <div class="px-6 py-4 border-b border-gray-200/80 dark:border-gray-700/80 flex justify-between items-center">
            <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('Pengguna Terbaru') }}</h3>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:text-blue-800">{{ __('Lihat Semua') }} →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Nama') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Role') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Unit / Departemen') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Tgl Daftar') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                    @foreach($recentUsers as $usr)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xs">
                                    {{ strtoupper(substr($usr->name, 0, 2)) }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $usr->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $usr->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @foreach($usr->roles as $role)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 mr-1 mb-1">
                                    {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $usr->department ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($usr->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ __('Aktif') }}</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ __('Nonaktif') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $usr->created_at->format('d M Y') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-ui.card>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Traffic Chart (Line)
            const trafficCtx = document.getElementById('trafficChart');
            if(trafficCtx) {
                const ctx = trafficCtx.getContext('2d');
                const gradientFill = ctx.createLinearGradient(0, 0, 0, 300);
                gradientFill.addColorStop(0, 'rgba(59, 130, 246, 0.4)'); // blue-500
                gradientFill.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

                new Chart(trafficCtx, {
                    type: 'line',
                    data: {
                        labels: @json($trafficChart['labels']),
                        datasets: [{
                            label: 'Audit Logs',
                            data: @json($trafficChart['data']),
                            borderColor: '#3b82f6',
                            backgroundColor: gradientFill,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#3b82f6',
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // Role Distribution (Pie)
            const roleCtx = document.getElementById('roleChart');
            if(roleCtx) {
                new Chart(roleCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($roleDistribution['labels']),
                        datasets: [{
                            data: @json($roleDistribution['data']),
                            backgroundColor: [
                                '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', 
                                '#06b6d4', '#ec4899', '#6366f1', '#14b8a6', '#f97316'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: { boxWidth: 12, font: { size: 10 } }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
