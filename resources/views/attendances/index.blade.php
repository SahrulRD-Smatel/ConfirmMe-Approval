<x-app-layout>
    <x-slot name="title">{{ __('Absensi') }}</x-slot>

    <div class="space-y-7" x-data="{
        latitude: null,
        longitude: null,
        locationReady: false,
        locationError: '',
        selfieData: null,
        selfieActive: false,
        stream: null,
        isWfh: false,
        loading: false,

        async initLocation() {
            if (!navigator.geolocation) {
                this.locationError = '{{ __('Geolocation tidak didukung di browser ini.') }}';
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.latitude = pos.coords.latitude;
                    this.longitude = pos.coords.longitude;
                    this.locationReady = true;
                },
                (err) => {
                    this.locationError = '{{ __('Tidak dapat mendapatkan lokasi. Pastikan GPS aktif.') }}';
                    console.warn('Geolocation error:', err.message);
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        },

        async startCamera() {
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user', width: 480, height: 480 }
                });
                this.$refs.video.srcObject = this.stream;
                this.selfieActive = true;
            } catch (e) {
                showAlert('{{ __('Tidak dapat mengakses kamera. Pastikan izin kamera diaktifkan.') }}');
            }
        },

        capturePhoto() {
            const canvas = this.$refs.canvas;
            const video = this.$refs.video;
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            canvas.toBlob((blob) => {
                const file = new File([blob], 'selfie.jpg', { type: 'image/jpeg' });
                const dt = new DataTransfer();
                dt.items.add(file);
                this.$refs.photoInput.files = dt.files;
                this.selfieData = canvas.toDataURL('image/jpeg');
                this.stopCamera();
            }, 'image/jpeg', 0.8);
        },

        retakePhoto() {
            this.selfieData = null;
            this.$refs.photoInput.value = '';
            this.startCamera();
        },

        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(t => t.stop());
                this.stream = null;
            }
            this.selfieActive = false;
        },

        init() {
            this.initLocation();
        }
    }">

        {{-- Header --}}
        <x-ui.card class="relative mb-7 overflow-hidden rounded-3xl">
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
                        <x-icon name="finger-print" class="w-5 h-5 text-primary-600" />
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Absensi') }}</h1>
                        <p class="text-sm text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                </div>
            </div>
        </x-ui.card>



        {{-- Today Status Cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            {{-- Check-In Card --}}
            <x-ui.stat-card title="{{ __('Check In') }}" 
                :value="$todayCheckIn ? $todayCheckIn->created_at->format('H:i') : __('Belum check-in')" 
                icon="arrow-right-on-rectangle" 
                :color="$todayCheckIn ? 'success' : 'gray'"
                value-size="text-2xl font-bold tracking-tight">
                @if($todayCheckIn)
                    <div class="mt-1">
                        <x-ui.badge :status="$todayCheckIn->status_color" size="sm">
                            {{ $todayCheckIn->status_label }}
                        </x-ui.badge>
                    </div>
                @endif
            </x-ui.stat-card>

            {{-- Check-Out Card --}}
            <x-ui.stat-card title="{{ __('Check Out') }}" 
                :value="$todayCheckOut ? $todayCheckOut->created_at->format('H:i') : __('Belum check-out')" 
                icon="arrow-left-on-rectangle" 
                :color="$todayCheckOut ? 'primary' : 'gray'"
                value-size="text-2xl font-bold tracking-tight" />

            {{-- Duration Card --}}
            <x-ui.stat-card title="{{ __('Durasi Kerja') }}" 
                :value="$workDuration" 
                icon="clock" 
                color="primary"
                value-size="text-2xl font-bold tracking-tight" />
        </div>

        {{-- Action Section --}}
        @if(!$todayCheckIn || !$todayCheckOut)
            <x-ui.card>
                <h2 class="mb-5 flex items-center gap-2 text-base font-semibold tracking-tight text-gray-900 dark:text-white">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                    {{ !$todayCheckIn ? __('Check In') : __('Check Out') }}
                </h2>

                <form action="{{ !$todayCheckIn ? route('attendance.check-in') : route('attendance.check-out') }}"
                      method="POST" enctype="multipart/form-data" @submit="loading = true">
                    @csrf

                    {{-- Hidden GPS fields --}}
                    <input type="hidden" name="latitude" :value="latitude">
                    <input type="hidden" name="longitude" :value="longitude">

                    {{-- GPS Status --}}
                    <div class="mb-4 p-3 rounded-xl" :class="locationReady ? 'bg-green-50' : 'bg-yellow-50'">
                        <div class="flex items-center gap-2 text-sm">
                            <template x-if="locationReady">
                                <span class="text-green-700 flex items-center gap-1">
                                    <x-icon name="map-pin" class="w-4 h-4" /> {{ __('Lokasi GPS terdeteksi') }}
                                </span>
                            </template>
                            <template x-if="!locationReady && !locationError">
                                <span class="text-yellow-700 flex items-center gap-1">
                                    <x-icon name="map-pin" class="w-4 h-4 animate-pulse" /> {{ __('Mendeteksi lokasi...') }}
                                </span>
                            </template>
                            <template x-if="locationError">
                                <span class="text-red-600 flex items-center gap-1" x-text="locationError"></span>
                            </template>
                        </div>
                    </div>

                    {{-- WFH Toggle (only for check-in) --}}
                    @if(!$todayCheckIn)
                        <div class="mb-4 p-4 bg-blue-50 rounded-xl">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_wfh" x-model="isWfh" value="1"
                                       class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <span class="text-sm font-medium text-blue-900">{{ __('Work From Home (WFH)') }}</span>
                                    <p class="text-xs text-blue-600">{{ __('Centang jika bekerja dari rumah') }}</p>
                                </div>
                            </label>
                        </div>
                    @endif

                    {{-- Camera / Selfie --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-1.5">
                            <x-icon name="camera" class="w-4 h-4 text-gray-500" />
                            <span>{{ __('Foto Selfie') }}</span> <span class="text-red-500">*</span>
                        </label>

                        <input type="file" name="photo" x-ref="photoInput" accept="image/*" class="hidden">

                        {{-- Camera Preview --}}
                        <div class="relative mx-auto aspect-square w-full max-w-xs overflow-hidden rounded-3xl bg-gray-950 shadow-soft">
                            <video x-ref="video" autoplay playsinline
                                   x-show="selfieActive && !selfieData"
                                   class="w-full h-full object-cover"></video>

                            <canvas x-ref="canvas" class="hidden"></canvas>

                            {{-- Preview captured photo --}}
                            <template x-if="selfieData">
                                <img :src="selfieData" class="w-full h-full object-cover">
                            </template>

                            {{-- Start camera placeholder --}}
                            <div x-show="!selfieActive && !selfieData"
                                 class="absolute inset-0 flex flex-col items-center justify-center text-white cursor-pointer hover:bg-gray-800 transition"
                                 @click="startCamera()">
                                <x-icon name="camera" class="w-12 h-12 text-gray-400 mb-2" />
                                <span class="text-sm text-gray-400">{{ __('Tap untuk buka kamera') }}</span>
                            </div>
                        </div>

                        {{-- Camera buttons --}}
                        <div class="flex justify-center gap-3 mt-3">
                            <button type="button" x-show="selfieActive && !selfieData" @click="capturePhoto()"
                                    class="px-6 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-xl hover:bg-primary-700 transition flex items-center gap-2">
                                <x-icon name="camera" class="w-4 h-4" /> {{ __('Ambil Foto') }}
                            </button>
                            <button type="button" x-show="selfieData" @click="retakePhoto()"
                                    class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-300 transition">
                                {{ __('Ulangi Foto') }}
                            </button>
                        </div>

                        @error('photo')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="mb-4" x-show="isWfh || {{ $todayCheckIn ? 'true' : 'false' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Catatan') }} <span x-show="isWfh" class="text-red-500">*</span>
                        </label>
                        <textarea name="notes" rows="2"
                                  class="w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20/10 text-sm"
                                  :placeholder="isWfh ? '{{ __('Jelaskan alasan WFH...') }}' : '{{ __('Catatan tambahan (opsional)') }}'"></textarea>
                        @error('notes')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <button type="submit" :disabled="loading || !selfieData"
                            class="w-full py-3 rounded-xl text-white font-bold text-sm transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            :class="loading ? 'bg-gray-400' : 'bg-primary-600 hover:bg-primary-700 hover:shadow-lg hover:shadow-primary-500/25'">
                        <template x-if="loading">
                            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </template>
                        <x-icon name="{{ !$todayCheckIn ? 'arrow-right-on-rectangle' : 'arrow-left-on-rectangle' }}" class="w-5 h-5" />
                        {{ !$todayCheckIn ? __('Check In Sekarang') : __('Check Out Sekarang') }}
                    </button>
                </form>
            </x-ui.card>
        @else
            {{-- All done for today --}}
            <x-ui.card class="text-center py-8">
                <div class="w-16 h-16 rounded-full bg-success-100 dark:bg-success-900/30 flex items-center justify-center mx-auto mb-4">
                    <x-icon name="check-badge" class="w-8 h-8 text-success-600 dark:text-success-400" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ __('Absensi Hari Ini Selesai!') }}</h3>
                <p class="text-sm text-gray-500">{{ __('Check-in') }}: {{ $todayCheckIn->created_at->format('H:i') }} → {{ __('Check-out') }}: {{ $todayCheckOut->created_at->format('H:i') }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ __('Durasi kerja') }}: <strong>{{ $workDuration }}</strong></p>
            </x-ui.card>
        @endif

        {{-- Monthly Summary --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                    {{ __('Ringkasan Bulan Ini') }}
                </h2>
                <a href="{{ route('attendance.history') }}" class="text-xs text-primary-600 hover:text-primary-800 font-medium">
                    {{ __('Lihat Riwayat') }} →
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-xl text-center border border-gray-200/80 dark:border-gray-700/80">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['total_days'] }}</p>
                    <p class="text-xs text-gray-500 font-medium">{{ __('Hari Masuk') }}</p>
                </div>
                <div class="p-4 bg-success-50 dark:bg-success-900/30 rounded-xl text-center border border-success-200 dark:border-success-800/30">
                    <p class="text-2xl font-bold text-success-700 dark:text-success-400">{{ $summary['on_time'] }}</p>
                    <p class="text-xs text-success-600 dark:text-success-500 font-medium">{{ __('Tepat Waktu') }}</p>
                </div>
                <div class="p-4 bg-danger-50 dark:bg-danger-900/30 rounded-xl text-center border border-danger-200 dark:border-danger-800/30">
                    <p class="text-2xl font-bold text-danger-700 dark:text-danger-400">{{ $summary['late'] }}</p>
                    <p class="text-xs text-danger-600 dark:text-danger-500 font-medium">{{ __('Terlambat') }}</p>
                </div>
                <div class="p-4 bg-primary-50 dark:bg-primary-900/30 rounded-xl text-center border border-primary-200 dark:border-primary-800/30">
                    <p class="text-2xl font-bold text-primary-700 dark:text-primary-400">{{ $summary['wfh'] }}</p>
                    <p class="text-xs text-primary-600 dark:text-primary-500 font-medium">{{ __('WFH') }}</p>
                </div>
            </div>
        </x-ui.card>

        {{-- Recent History --}}
        <x-ui.card>
            <h2 class="mb-5 flex items-center gap-2 text-base font-semibold tracking-tight text-gray-900 dark:text-white">
                <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                {{ __('Riwayat 7 Hari Terakhir') }}
            </h2>

            <div class="overflow-x-auto mt-4 border border-gray-200/80 dark:border-gray-700/80 rounded-xl">
                <table class="min-w-[720px] w-full divide-y divide-gray-200/80 dark:divide-gray-700/80">
                    <thead class="bg-gray-50/80 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Tanggal') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Check In') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Check Out') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200/80 dark:divide-gray-700/80">
                        @forelse($recentHistory as $date => $records)
                            @php
                                $checkIn = $records->firstWhere('type', 'check_in');
                                $checkOut = $records->firstWhere('type', 'check_out');
                            @endphp
                            <tr class="hover:bg-primary-50/60 dark:hover:bg-gray-800/80/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d M') }}
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
                                <td colspan="4" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-gray-50/80 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3 border border-gray-200/80 dark:border-gray-700/80">
                                            <x-icon name="clock" class="w-6 h-6 text-gray-400" />
                                        </div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">{{ __('Tidak ada riwayat') }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Belum ada riwayat absensi 7 hari terakhir.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
