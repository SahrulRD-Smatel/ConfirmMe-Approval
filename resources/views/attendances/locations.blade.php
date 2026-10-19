<x-app-layout>
    <x-slot name="title">{{ __('Lokasi Kantor') }}</x-slot>

    <div class="space-y-7">
        {{-- Header --}}
        <div class="flex flex-col gap-4 rounded-2xl border border-gray-200/80 bg-white p-5 shadow-soft dark:border-gray-700/80 dark:bg-gray-800 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-3">
                <div class="rounded-xl bg-primary-50 p-3 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400"><x-icon name="map-pin" class="h-6 w-6" /></div>
                <div><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-primary-600 dark:text-primary-400">{{ __('Attendance geofencing') }}</p><h1 class="mt-1 text-xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('Kelola Lokasi Kantor') }}</h1><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Atur titik GPS kantor untuk validasi absensi') }} <span class="font-semibold text-gray-700 dark:text-gray-300">({{ $locations->count() }} {{ __('lokasi') }})</span></p></div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400"><span class="font-semibold text-gray-700 dark:text-gray-200">{{ __('Geofence') }}</span><br>{{ __('Validasi berbasis radius GPS') }}</div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
                <x-icon name="check-circle" class="w-5 h-5 text-green-500 flex-shrink-0" />
                <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            {{-- Add New Location --}}
            <x-ui.card>
                <h2 class="mb-5 flex items-center gap-2 text-base font-semibold tracking-tight text-gray-900 dark:text-white">
                    <x-icon name="plus-circle" class="w-5 h-5 text-primary-600" />
                    {{ __('Tambah Lokasi') }}
                </h2>

                <form action="{{ route('attendance-admin.locations.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nama Lokasi') }} *</label>
                        <input type="text" name="name" required class="w-full rounded-xl border-gray-300 text-sm focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20/10" placeholder="Kantor Pusat Jakarta">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Alamat') }}</label>
                        <textarea name="address" rows="2" class="w-full rounded-xl border-gray-300 text-sm focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20/10" placeholder="Jl. Example No. 1"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Latitude') }} *</label>
                            <input type="number" name="latitude" step="0.00000001" required class="w-full rounded-xl border-gray-300 text-sm" placeholder="-6.2088">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Longitude') }} *</label>
                            <input type="number" name="longitude" step="0.00000001" required class="w-full rounded-xl border-gray-300 text-sm" placeholder="106.8456">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Radius (meter)') }} *</label>
                        <input type="number" name="radius_meters" required min="50" max="5000" value="100" class="w-full rounded-xl border-gray-300 text-sm">
                        <p class="text-xs text-gray-400 mt-1">{{ __('Jarak toleransi dari titik GPS (50-5000m)') }}</p>
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-primary-600 text-white text-sm font-bold rounded-xl hover:bg-primary-700 transition">
                        {{ __('Simpan Lokasi') }}
                    </button>
                </form>
            </x-ui.card>

            {{-- Existing Locations --}}
            <div class="space-y-5 lg:col-span-2">
                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <x-icon name="map-pin" class="w-5 h-5 text-primary-600" />
                    {{ __('Daftar Lokasi') }} ({{ $locations->count() }})
                </h2>

                @forelse($locations as $location)
                    <x-ui.card class="{{ $location->is_active ? 'opacity-100' : 'opacity-60' }}">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-bold text-gray-900">{{ $location->name }}</h3>
                                @if($location->address)
                                    <p class="text-sm text-gray-500 mt-0.5">{{ $location->address }}</p>
                                @endif
                                <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <x-icon name="map-pin" class="w-3.5 h-3.5 shrink-0" />
                                        {{ $location->latitude }}, {{ $location->longitude }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <x-icon name="arrows-pointing-out" class="w-3.5 h-3.5 shrink-0" />
                                        Radius: {{ $location->radius_meters }}m
                                    </span>
                                </div>
                            </div>
                            <span class="text-xs font-medium px-2 py-1 rounded-full {{ $location->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $location->is_active ? __('Aktif') : __('Nonaktif') }}
                            </span>
                        </div>
                    </x-ui.card>
                @empty
                    <x-ui.card class="text-center py-8">
                        <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-3">
                            <x-icon name="map-pin" class="w-8 h-8 text-gray-300" />
                        </div>
                        <p class="text-sm text-gray-500">{{ __('Belum ada lokasi kantor') }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ __('Tambahkan lokasi kantor untuk fitur geofencing absensi') }}</p>
                    </x-ui.card>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
