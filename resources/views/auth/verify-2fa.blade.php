<x-guest-layout>
    <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-8 dark:bg-gray-950 sm:px-6">
        <div class="anim-fade-up w-full max-w-md">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">

                {{-- Header strip --}}
                <div class="border-b border-gray-100 px-6 py-5 dark:border-gray-800 sm:px-8">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-700">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Verifikasi OTP') }}</h1>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Keamanan dua langkah') }}</p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6 sm:px-8">
                    <p class="text-sm leading-6 text-gray-500 dark:text-gray-400">
                        {{ __('Masukkan kode OTP 6 digit yang telah dikirim ke email Anda untuk menyetujui dokumen berikut.') }}
                    </p>

                    {{-- Document summary --}}
                    <div class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/60">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-primary-700 shadow-sm dark:bg-gray-700 dark:text-primary-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $document->title }}</p>
                                <p class="mt-0.5 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $document->document_number }}</p>
                                @if($document->estimated_budget)
                                    @php
                                        $financeApproval2fa = $document->approvals()
                                            ->where('approver_role', 'finance')
                                            ->where('status', 'approved')
                                            ->whereNotNull('approved_amount')
                                            ->first();
                                    @endphp
                                    @if($financeApproval2fa && (float) $financeApproval2fa->approved_amount !== (float) $document->estimated_budget)
                                        <p class="mt-1.5 text-xs text-gray-400 line-through dark:text-gray-500">
                                            Rp {{ number_format($document->estimated_budget, 0, ',', '.') }}
                                        </p>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">
                                            Rp {{ number_format($financeApproval2fa->approved_amount, 0, ',', '.') }}
                                            <span class="text-xs font-normal text-gray-500 dark:text-gray-400">({{ __('Disetujui Finance') }})</span>
                                        </p>
                                    @else
                                        <p class="mt-1.5 text-sm font-bold text-gray-900 dark:text-white">
                                            Rp {{ number_format($document->estimated_budget, 0, ',', '.') }}
                                        </p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Debug OTP Display --}}
                    @if(isset($debugOtp) && $debugOtp)
                        <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-3.5 dark:border-amber-700/50 dark:bg-amber-900/20">
                            <div class="mb-2 flex items-center gap-2">
                                <span class="h-2 w-2 animate-pulse rounded-full bg-amber-500"></span>
                                <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-amber-700 dark:text-amber-300">{{ __('Debug Mode') }}</span>
                            </div>
                            <div class="rounded-lg border border-amber-100 bg-white px-3 py-2.5 text-center dark:border-amber-800/30 dark:bg-gray-800">
                                <span class="font-mono text-2xl font-bold tracking-[0.5em] text-amber-700 dark:text-amber-300">
                                    {{ $debugOtp }}
                                </span>
                            </div>
                        </div>
                    @endif

                    {{-- OTP Form --}}
                    <form method="POST" action="{{ route('2fa.verify', $document) }}" class="mt-6 space-y-5" id="otp-form">
                        @csrf

                        <div>
                            <div class="mb-3 flex items-center justify-between">
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Kode OTP') }}</label>
                                <span class="text-xs text-gray-400">{{ __('6 digit') }}</span>
                            </div>

                            <div class="flex justify-between gap-2 sm:gap-2.5" id="otp-container">
                                @for($i = 0; $i < 6; $i++)
                                    <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric"
                                        class="otp-digit h-14 w-full rounded-xl border-2 border-gray-200 bg-white text-center font-mono text-xl font-bold text-gray-900 outline-none transition-colors duration-150 hover:border-gray-300 focus:border-primary-600 focus:ring-4 focus:ring-primary-600/10 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:hover:border-gray-500 dark:focus:border-primary-400"
                                        data-index="{{ $i }}"
                                        autocomplete="off" aria-label="{{ __('Digit OTP ke-:n', ['n' => $i + 1]) }}">
                                @endfor
                            </div>

                            <input type="hidden" name="otp" id="otp-hidden">

                            @error('otp')
                                <div class="mt-3 flex items-center gap-2 text-sm text-red-600 dark:text-red-400">
                                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <button type="submit" id="verify-btn"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary-700 px-4 py-3.5 text-sm font-semibold text-white transition-all hover:-translate-y-0.5 hover:bg-primary-800 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-primary-600/20 active:translate-y-0 disabled:cursor-not-allowed disabled:opacity-50">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            {{ __('Verifikasi & Setujui') }}
                        </button>
                    </form>

                    {{-- Timer & Resend --}}
                    <div class="mt-6 space-y-3 border-t border-gray-100 pt-5 text-center dark:border-gray-800">
                        <div class="flex items-center justify-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ __('Kode berlaku') }} <span id="countdown" class="font-semibold tabular-nums text-primary-700 dark:text-primary-400">10:00</span></span>
                        </div>

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Tidak menerima kode?') }}
                            <form method="POST" action="{{ route('2fa.resend', $document) }}" class="mt-1 inline">
                                @csrf
                                <button type="submit" class="font-semibold text-primary-700 transition-colors hover:text-primary-600 hover:underline dark:text-primary-400 dark:hover:text-primary-300">
                                    {{ __('Kirim ulang OTP') }}
                                </button>
                            </form>
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-5 text-center">
                <a href="{{ route('approvals.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm text-gray-500 transition-colors hover:bg-white hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-gray-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('Kembali ke daftar approval') }}
                </a>
            </div>
        </div>
    </div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const digits = document.querySelectorAll('.otp-digit');
            const hiddenInput = document.getElementById('otp-hidden');
            const form = document.getElementById('otp-form');
            const verifyBtn = document.getElementById('verify-btn');

            // Auto-focus first input
            if (digits[0]) digits[0].focus();

            // Combine all digits into hidden input
            function updateHidden() {
                const otp = Array.from(digits).map(d => d.value).join('');
                hiddenInput.value = otp;

                // Visual feedback when all 6 digits filled
                if (otp.length === 6) {
                    verifyBtn.classList.add('animate-pulse');
                } else {
                    verifyBtn.classList.remove('animate-pulse');
                }
            }

            digits.forEach((digit, index) => {
                // Handle input event (fires after value changes)
                digit.addEventListener('input', function(e) {
                    // Strip non-numeric
                    let val = this.value.replace(/[^0-9]/g, '');

                    if (val.length > 1) {
                        // If multiple chars entered (e.g. autocomplete), distribute them
                        const chars = val.split('');
                        chars.forEach((char, i) => {
                            const targetIndex = index + i;
                            if (targetIndex < 6 && digits[targetIndex]) {
                                digits[targetIndex].value = char;
                            }
                        });
                        const nextFocus = Math.min(index + chars.length, 5);
                        digits[nextFocus].focus();
                    } else {
                        this.value = val;
                        // Move to next field
                        if (val && index < 5) {
                            digits[index + 1].focus();
                        }
                    }

                    updateHidden();
                });

                // Handle keydown for backspace and arrow navigation
                digit.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace') {
                        if (this.value) {
                            // Clear current field
                            this.value = '';
                            updateHidden();
                        } else if (index > 0) {
                            // Move to previous field and clear it
                            e.preventDefault();
                            digits[index - 1].value = '';
                            digits[index - 1].focus();
                            updateHidden();
                        }
                    } else if (e.key === 'Delete') {
                        this.value = '';
                        updateHidden();
                    } else if (e.key === 'ArrowLeft' && index > 0) {
                        e.preventDefault();
                        digits[index - 1].focus();
                    } else if (e.key === 'ArrowRight' && index < 5) {
                        e.preventDefault();
                        digits[index + 1].focus();
                    }
                });

                // Handle paste (works from any field)
                digit.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pasteData = (e.clipboardData || window.clipboardData)
                        .getData('text')
                        .replace(/[^0-9]/g, '')
                        .slice(0, 6);

                    if (pasteData.length > 0) {
                        pasteData.split('').forEach((char, i) => {
                            if (digits[i]) {
                                digits[i].value = char;
                            }
                        });
                        const focusIndex = Math.min(pasteData.length, 5);
                        digits[focusIndex].focus();
                        updateHidden();
                    }
                });

                // Select content on focus for easy overwrite
                digit.addEventListener('focus', function() {
                    // Small delay to ensure selection works on mobile
                    setTimeout(() => this.select(), 0);
                });

                // Prevent non-numeric input via keypress (additional safety)
                digit.addEventListener('keypress', function(e) {
                    if (!/[0-9]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Enter') {
                        e.preventDefault();
                    }
                });
            });

            // Countdown timer (10 minutes)
            const countdownEl = document.getElementById('countdown');
            let timeLeft = 10 * 60;

            function updateCountdown() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                countdownEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

                if (timeLeft <= 60) {
                    countdownEl.classList.remove('text-primary-600', 'dark:text-primary-400');
                    countdownEl.classList.add('text-red-600', 'dark:text-red-400');
                }

                if (timeLeft <= 0) {
                    countdownEl.textContent = '{{ __('Kadaluarsa') }}';
                    countdownEl.classList.add('text-red-600');
                    clearInterval(timer);
                    return;
                }

                timeLeft--;
            }

            const timer = setInterval(updateCountdown, 1000);
            updateCountdown();

            // Ensure hidden input is synced on form submit
            form.addEventListener('submit', function(e) {
                updateHidden();
                if (hiddenInput.value.length !== 6) {
                    e.preventDefault();
                    // Highlight empty fields
                    digits.forEach(d => {
                        if (!d.value) {
                            d.classList.add('border-red-500');
                            setTimeout(() => d.classList.remove('border-red-500'), 2000);
                        }
                    });
                }
            });
        });
    </script>
    @endpush
</x-guest-layout>
