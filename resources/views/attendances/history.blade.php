<x-app-layout>
    <x-slot name="title">{{ __('Riwayat Absensi') }}</x-slot>

    <div class="space-y-7">
        {{-- Header --}}
        <x-ui.card class="relative overflow-hidden">
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Riwayat Absensi') }}</h1>
                    <p class="text-sm text-gray-500">{{ auth()->user()->name }}</p>
                </div>

                {{-- Month Selector --}}
                <form method="GET" class="flex items-center gap-2">
                    <select name="month" class="rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20/10">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                    <select name="year" class="rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20/10">
                        @for($y = now()->year + 2; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-xl hover:bg-primary-700 transition">
                        {{ __('Filter') }}
                    </button>
                </form>
            </div>
        </x-ui.card>

        {{-- Monthly Stats --}}
        <x-ui.card>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
                <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-xl text-center border border-gray-200/80 dark:border-gray-700/80">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-500 font-medium">{{ __('Total Masuk') }}</p>
                </div>
                <div class="p-4 bg-success-50 dark:bg-success-900/30 rounded-xl text-center border border-success-200 dark:border-success-800/30">
                    <p class="text-2xl font-bold text-success-700 dark:text-success-400">{{ $stats['on_time'] }}</p>
                    <p class="text-xs text-success-600 dark:text-success-500 font-medium">{{ __('Tepat Waktu') }}</p>
                </div>
                <div class="p-4 bg-danger-50 dark:bg-danger-900/30 rounded-xl text-center border border-danger-200 dark:border-danger-800/30">
                    <p class="text-2xl font-bold text-danger-700 dark:text-danger-400">{{ $stats['late'] }}</p>
                    <p class="text-xs text-danger-600 dark:text-danger-500 font-medium">{{ __('Terlambat') }}</p>
                </div>
                <div class="p-4 bg-primary-50 dark:bg-primary-900/30 rounded-xl text-center border border-primary-200 dark:border-primary-800/30">
                    <p class="text-2xl font-bold text-primary-700 dark:text-primary-400">{{ $stats['wfh'] }}</p>
                    <p class="text-xs text-primary-600 dark:text-primary-500 font-medium">{{ __('WFH') }}</p>
                </div>
                <div class="p-4 bg-warning-50 dark:bg-warning-900/30 rounded-xl text-center border border-warning-200 dark:border-warning-800/30">
                    <p class="text-2xl font-bold text-warning-700 dark:text-warning-400">{{ $stats['early_leave'] }}</p>
                    <p class="text-xs text-warning-600 dark:text-warning-500 font-medium">{{ __('Pulang Awal') }}</p>
                </div>
            </div>
        </x-ui.card>

        {{-- Daily Records --}}
        <x-ui.card>
            <h2 class="mb-5 flex items-center gap-2 text-base font-semibold tracking-tight text-gray-900 dark:text-white">
                <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                {{ __('Detail Harian') }}
            </h2>

            <div class="overflow-x-auto border border-gray-200/80 dark:border-gray-700/80 rounded-xl">
                <table class="min-w-[900px] w-full divide-y divide-gray-200/80 dark:divide-gray-700/80">
                    <thead class="bg-gray-50/80 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Tanggal') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Check In') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Check Out') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Durasi') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Lokasi / Catatan') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200/80 dark:divide-gray-700/80">
                        @forelse($attendances as $date => $records)
                            @php
                                $checkIn = $records->firstWhere('type', 'check_in');
                                $checkOut = $records->firstWhere('type', 'check_out');
                            @endphp
                            <tr class="hover:bg-primary-50/60 dark:hover:bg-gray-800/80/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $checkIn ? $checkIn->created_at->format('H:i') : '-' }}
                                    @if($checkIn && $checkIn->is_wfh)
                                        <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400">WFH</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $checkOut ? $checkOut->created_at->format('H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($checkIn && $checkOut)
                                        {{ $checkIn->created_at->diffForHumans($checkOut->created_at, true) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                    <div class="truncate">{{ $checkIn->location_name ?? '-' }}</div>
                                    @if($checkIn && $checkIn->notes)
                                        <div class="text-xs text-gray-400 mt-1 truncate italic">"{{ $checkIn->notes }}"</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($checkIn)
                                        <x-ui.badge :status="$checkIn->status_color" size="sm">
                                            {{ $checkIn->status_label }}
                                        </x-ui.badge>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-gray-50/80 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3 border border-gray-200/80 dark:border-gray-700/80">
                                            <x-icon name="clock" class="w-6 h-6 text-gray-400" />
                                        </div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">{{ __('Tidak ada riwayat') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Tidak ada data absensi untuk bulan ini.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($attendances->hasPages())
                <div class="mt-4">
                    {{ $attendances->links() }}
                </div>
            @endif
        </x-ui.card>

        {{-- Back to attendance --}}
        <div class="text-center">
            <a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-primary-600 hover:text-primary-800">
                ← {{ __('Kembali ke Absensi') }}
            </a>
        </div>
    </div>
</x-app-layout>
