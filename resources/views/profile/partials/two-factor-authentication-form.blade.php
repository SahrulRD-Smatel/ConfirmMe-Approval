<section>
    <header>
        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-400">{{ __('Two-factor security') }}</p>
        <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">
            {{ __('Autentikasi Dua Langkah (2FA)') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Lindungi persetujuan dokumen Anda menggunakan kode 6 digit instan dari aplikasi Google Authenticator, Microsoft Authenticator, atau 2FAS (Kompatibel dengan Android, iPhone, dan Huawei).') }}
        </p>
    </header>

    {{-- Status Alerts --}}
    @if (session('status') === 'two-factor-enabled')
        <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900/60 dark:bg-emerald-950/30">
            <div class="flex items-start gap-3">
                <x-icon name="check-circle" class="h-5 w-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
                <div class="text-sm text-emerald-800 dark:text-emerald-200">
                    <p class="font-bold">{{ __('Authenticator Berhasil Diaktifkan!') }}</p>
                    <p class="mt-0.5 text-xs text-emerald-700 dark:text-emerald-300">
                        {{ __('Mulai sekarang, persetujuan dokumen akan diverifikasi langsung melalui aplikasi autentikator di ponsel Anda tanpa perlu menunggu email.') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if (session('status') === 'two-factor-disabled')
        <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs text-gray-600 dark:text-gray-300">
                {{ __('Autentikasi dua langkah (2FA) telah dinonaktifkan dari akun Anda.') }}
            </p>
        </div>
    @endif

    {{-- Recovery Codes Flash --}}
    @if (session('recovery_codes'))
        <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/60 dark:bg-amber-950/30">
            <div class="flex items-start gap-3">
                <x-icon name="key" class="h-5 w-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" />
                <div class="w-full text-sm text-amber-900 dark:text-amber-200">
                    <p class="font-bold">{{ __('Simpan Kode Pemulihan Darurat Anda') }}</p>
                    <p class="mt-1 text-xs text-amber-800 dark:text-amber-300">
                        {{ __('Simpan kode-kode ini di tempat yang aman. Jika ponsel Anda hilang atau tidak dapat diakses, Anda dapat menggunakan salah satu kode ini untuk verifikasi approval.') }}
                    </p>
                    <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-2 bg-white dark:bg-gray-900 p-3 rounded-lg border border-amber-200 dark:border-amber-800 font-mono text-xs text-center font-bold text-gray-800 dark:text-gray-100 select-all">
                        @foreach(session('recovery_codes') as $code)
                            <div class="py-1 px-2 bg-gray-50 dark:bg-gray-800 rounded">{{ $code }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- State 1: 2FA Already Active --}}
    @if ($user->hasTwoFactorEnabled())
        <div class="mt-6 space-y-4"
            x-init="
                if (window.location.hash === '#two-factor-auth' || {{ (session('status') && str_starts_with(session('status'), 'two-factor')) || session('recovery_codes') ? 'true' : 'false' }}) {
                    $nextTick(() => {
                        document.getElementById('two-factor-auth')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    });
                }
            ">
            <div class="flex items-center justify-between p-4 rounded-xl border border-emerald-200/80 bg-emerald-50/50 dark:border-emerald-900/40 dark:bg-emerald-950/20">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-600 text-white">
                        <x-icon name="shield-check" class="h-6 w-6" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ __('Authenticator Aktif') }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300">
                                {{ __('Terverifikasi') }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ __('Aktif sejak') }}: {{ $user->two_factor_confirmed_at?->translatedFormat('d F Y, H:i') }} WIB
                        </p>
                    </div>
                </div>
            </div>

            {{-- Disable Modal / Form --}}
            <div x-data="{ confirmingDisable: false }">
                <button type="button" @click="confirmingDisable = true"
                    class="inline-flex items-center gap-1.5 px-4 py-2 border border-danger-300 dark:border-danger-700 rounded-xl text-xs font-semibold text-danger-700 dark:text-danger-400 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors">
                    <x-icon name="no-symbol" class="w-4 h-4" />
                    {{ __('Nonaktifkan Authenticator') }}
                </button>

                <div x-show="confirmingDisable" x-cloak class="mt-4 p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 space-y-3">
                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300">
                        {{ __('Masukkan kata sandi Anda untuk mengonfirmasi penonaktifan Authenticator 2FA:') }}
                    </p>
                    <form method="POST" action="{{ route('profile.2fa.disable') }}" class="space-y-3">
                        @csrf
                        <div>
                            <input type="password" name="password" required placeholder="{{ __('Kata sandi saat ini') }}"
                                class="block w-full max-w-sm px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            @error('password', 'twoFactorDisable')
                                <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="px-3.5 py-1.5 bg-danger-600 hover:bg-danger-700 text-white rounded-lg text-xs font-semibold transition-colors">
                                {{ __('Konfirmasi Nonaktifkan') }}
                            </button>
                            <button type="button" @click="confirmingDisable = false" class="px-3.5 py-1.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-xs font-semibold transition-colors">
                                {{ __('Batal') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    {{-- State 2 & 3: Setup In-Progress or Inactive (Handled smoothly with Alpine.js) --}}
    @else
        <div x-data="{
            loading: false,
            setupReady: {{ (isset($qrSvg) && $qrSvg) ? 'true' : 'false' }},
            qrSvg: {!! json_encode((string)($qrSvg ?? '')) !!},
            secretKey: '{{ $secretKey ?? '' }}',
            chunkedSecret: '{{ isset($secretKey) ? chunk_split($secretKey, 4, ' ') : '' }}',
            errorMessage: '',
            async startSetup() {
                this.loading = true;
                this.errorMessage = '';
                try {
                    const res = await fetch('{{ route('profile.2fa.enable') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.qrSvg = data.qrSvg;
                        this.secretKey = data.secretKey;
                        this.chunkedSecret = data.chunkedSecret;
                        this.setupReady = true;
                    } else {
                        this.errorMessage = data.message || '{{ __('Gagal menyiapkan autentikator.') }}';
                    }
                } catch (err) {
                    this.$refs.enableForm.submit();
                } finally {
                    this.loading = false;
                }
            },
            async cancelSetup() {
                this.loading = true;
                try {
                    await fetch('{{ route('profile.2fa.cancel') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    this.setupReady = false;
                    this.qrSvg = '';
                    this.secretKey = '';
                    this.chunkedSecret = '';
                } catch (err) {
                    this.$refs.cancelForm.submit();
                } finally {
                    this.loading = false;
                }
            }
        }"
        x-init="
            if (window.location.hash === '#two-factor-auth' || {{ (session('status') && str_starts_with(session('status'), 'two-factor')) || session('recovery_codes') || $errors->twoFactorConfirm->any() || $errors->twoFactorDisable->any() ? 'true' : 'false' }}) {
                $nextTick(() => {
                    document.getElementById('two-factor-auth')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
            }
        ">

            {{-- Setup Step Panel --}}
            <div x-show="setupReady" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="mt-6 space-y-5 rounded-2xl border border-gray-200/80 bg-gray-50/60 p-5 dark:border-gray-800 dark:bg-gray-800/40">
                <div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-primary-100 text-primary-800 dark:bg-primary-900/60 dark:text-primary-300">
                        {{ __('Langkah 1 dari 2') }}
                    </span>
                    <h3 class="mt-2 text-sm font-bold text-gray-900 dark:text-white">
                        {{ __('Pindai QR Code di Aplikasi Authenticator Ponsel') }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                        {{ __('Buka aplikasi Google Authenticator, Microsoft Authenticator, atau 2FAS di ponsel Anda (Android, iPhone, atau Huawei AppGallery), lalu pilih \"Scan QR Code\".') }}
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-5 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="p-2 bg-white rounded-lg border border-gray-100 shadow-sm shrink-0" x-html="qrSvg">
                        @if(isset($qrSvg) && $qrSvg)
                            {!! $qrSvg !!}
                        @endif
                    </div>

                    <div class="space-y-2 text-center sm:text-left">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ __('Tidak bisa memindai? Masukkan kode rahasia ini secara manual di aplikasi ponsel Anda:') }}
                        </p>
                        <div class="flex items-center justify-center sm:justify-start gap-2">
                            <code class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 font-mono text-sm font-bold text-gray-900 dark:text-white rounded-lg select-all tracking-wider" x-text="chunkedSecret">
                                {{ isset($secretKey) ? chunk_split($secretKey, 4, ' ') : '' }}
                            </code>
                        </div>
                        <p class="text-[11px] text-gray-400">
                            {{ __('Tipe Akun: Time-based (TOTP) · Periode: 30 Detik') }}
                        </p>
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-primary-100 text-primary-800 dark:bg-primary-900/60 dark:text-primary-300">
                        {{ __('Langkah 2 dari 2') }}
                    </span>
                    <h3 class="mt-2 text-sm font-bold text-gray-900 dark:text-white">
                        {{ __('Masukkan 6 Digit Kode Konfirmasi') }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ __('Masukkan kode 6 digit yang baru saja muncul di aplikasi autentikator ponsel Anda untuk mengonfirmasi aktivasi:') }}
                    </p>

                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        <form method="POST" action="{{ route('profile.2fa.confirm') }}" class="flex flex-wrap items-center gap-3">
                            @csrf
                            <div>
                                <input type="text" name="code" required maxlength="6" inputmode="numeric" pattern="[0-9]*" placeholder="123456" autocomplete="one-time-code"
                                    class="block w-40 px-3 py-2 text-center font-mono text-base font-bold tracking-widest border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                @error('code', 'twoFactorConfirm')
                                    <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-xs font-semibold transition-colors">
                                {{ __('Verifikasi & Aktifkan') }}
                            </button>
                        </form>

                        <form x-ref="cancelForm" method="POST" action="{{ route('profile.2fa.cancel') }}" @submit.prevent="cancelSetup" class="inline">
                            @csrf
                            <button type="submit" :disabled="loading" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-xl text-xs font-semibold transition-colors">
                                {{ __('Batal') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Initial Inactive Panel --}}
            <div x-show="!setupReady" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="mt-5 space-y-4">
                <template x-if="errorMessage">
                    <div class="p-3 bg-danger-50 dark:bg-danger-900/20 text-danger-700 dark:text-danger-300 rounded-xl text-xs font-medium" x-text="errorMessage"></div>
                </template>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="p-3.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/70 dark:bg-gray-800/60">
                        <p class="text-xs font-bold text-gray-900 dark:text-white">{{ __('Instan & Tanpa Delay') }}</p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Kode langsung tampil di layar ponsel. Tidak perlu menunggu kiriman email yang lambat.') }}</p>
                    </div>
                    <div class="p-3.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/70 dark:bg-gray-800/60">
                        <p class="text-xs font-bold text-gray-900 dark:text-white">{{ __('Bekerja 100% Offline') }}</p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Ponsel Anda tidak memerlukan kuota internet saat menghasilkan kode 6 digit.') }}</p>
                    </div>
                    <div class="p-3.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/70 dark:bg-gray-800/60">
                        <p class="text-xs font-bold text-gray-900 dark:text-white">{{ __('Multi-Platform Universal') }}</p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Mendukung Android (Google Play), iPhone (iOS), dan Huawei (AppGallery / 2FAS).') }}</p>
                    </div>
                </div>

                <form x-ref="enableForm" method="POST" action="{{ route('profile.2fa.enable') }}" @submit.prevent="startSetup">
                    @csrf
                    <button type="submit" :disabled="loading" class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 disabled:opacity-60 text-white rounded-xl text-xs font-semibold shadow-sm transition-all hover:-translate-y-0.5">
                        <template x-if="!loading">
                            <x-icon name="shield-check" class="w-4 h-4" />
                        </template>
                        <template x-if="loading">
                            <svg class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="loading ? '{{ __('Menyiapkan QR Code...') }}' : '{{ __('Aktifkan Authenticator (2FA)') }}'">
                            {{ __('Aktifkan Authenticator (2FA)') }}
                        </span>
                    </button>
                </form>
            </div>
        </div>
    @endif
</section>
