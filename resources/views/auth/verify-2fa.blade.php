<x-guest-layout>
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-gray-50 px-4 py-8 dark:bg-gray-950 sm:px-6 lg:px-8">
        {{-- Background decorative elements --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -right-40 -top-40 h-80 w-80 rounded-full bg-primary-200/20 blur-3xl dark:bg-primary-800/10"></div>
            <div class="absolute -bottom-40 -left-40 h-80 w-80 rounded-full bg-primary-300/20 blur-3xl dark:bg-primary-700/10"></div>
        </div>

        <div class="relative z-10 w-full max-w-5xl">
            <div class="overflow-hidden rounded-[2rem] border border-gray-200/80 bg-white shadow-soft-lg dark:border-gray-800/80 dark:bg-gray-900 lg:grid lg:grid-cols-[0.86fr_1.14fr]">
                {{-- Context Panel --}}
                <aside class="relative overflow-hidden bg-primary-900 p-6 text-white sm:p-8 lg:p-10">
                    <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full border border-white/10"></div>
                    <div class="absolute -bottom-20 -left-14 h-56 w-56 rounded-full border border-white/5"></div>

                    <div class="relative flex h-full flex-col">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/20">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2V7a2 2 0 00-2-2h-1V4a3 3 0 00-6 0v1H6a2 2 0 00-2 2v12a2 2 0 002 2zm5-15h2" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">ConfirmMe Secure</p>
                                <p class="mt-0.5 text-sm font-semibold">Approval verification</p>
                            </div>
                        </div>

                        <div class="mt-10 hidden lg:block">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/60">Step 02 of 02</p>
                            <h2 class="mt-3 font-display text-3xl font-bold leading-tight">Satu langkah lagi sebelum persetujuan.</h2>
                            <p class="mt-4 max-w-xs text-sm leading-6 text-white/70">Gunakan kode sekali pakai dari email Anda untuk menyelesaikan verifikasi keamanan.</p>
                        </div>

                        {{-- Document Info Card --}}
                        <div class="mt-8 rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm lg:mt-auto">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-white text-primary-700 shadow-sm">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-white/60">{{ __('Dokumen yang akan disetujui') }}</p>
                                    <p class="mt-1 truncate text-sm font-semibold text-white">{{ $document->title }}</p>
                                    <p class="font-mono text-xs text-white/60">{{ $document->document_number }}</p>
                                    @if($document->estimated_budget)
                                        @php
                                            $financeApproval2fa = $document->approvals()
                                                ->where('approver_role', 'finance')
                                                ->where('status', 'approved')
                                                ->whereNotNull('approved_amount')
                                                ->first();
                                        @endphp
                                        @if($financeApproval2fa && (float) $financeApproval2fa->approved_amount !== (float) $document->estimated_budget)
                                            <p class="mt-2 text-xs text-white/40 line-through">
                                                Rp {{ number_format($document->estimated_budget, 0, ',', '.') }}
                                            </p>
                                            <p class="text-sm font-bold text-white">
                                                Rp {{ number_format($financeApproval2fa->approved_amount, 0, ',', '.') }}
                                                <span class="text-xs font-normal text-yellow-200">({{ __('Disetujui Finance') }})</span>
                                            </p>
                                        @else
                                            <p class="mt-2 text-sm font-bold text-white">
                                                Rp {{ number_format($document->estimated_budget, 0, ',', '.') }}
                                            </p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                {{-- Verification Panel --}}
                <section class="p-6 sm:p-8 lg:p-10">
                    <div class="mx-auto max-w-md">
                        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-primary-600 dark:text-primary-400">
                            <span class="h-2 w-2 rounded-full bg-primary-500"></span>
                            Two-factor verification
                        </div>

                        {{-- Header --}}
                        <div class="mt-4">
                            <h2 class="font-display text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                                {{ __('Verifikasi 2FA') }}
                            </h2>
                            <p class="mt-3 text-sm leading-6 text-gray-500 dark:text-gray-400">
                                {{ __('Masukkan kode OTP 6 digit yang telah dikirim ke email Anda') }}
                            </p>
                        </div>

                        {{-- Debug OTP Display --}}
                        @if(isset($debugOtp) && $debugOtp)
                            <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-3.5 dark:border-amber-700/50 dark:bg-amber-900/20">
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
                        <form method="POST" action="{{ route('2fa.verify', $document) }}" class="mt-8 space-y-6" id="otp-form">
                            @csrf

                            <div>
                                <div class="mb-3 flex items-center justify-between">
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Kode OTP</label>
                                    <span class="text-xs text-gray-400">6 digit</span>
                                </div>

                                {{-- OTP Individual Boxes --}}
                                <div class="flex justify-between gap-2 sm:gap-3" id="otp-container">
                                    @for($i = 0; $i < 6; $i++)
                                        <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric"
                                            class="otp-digit h-14 w-full rounded-xl border-2 border-gray-200 bg-gray-50 text-center font-mono text-xl font-bold text-gray-900 shadow-sm outline-none transition-all duration-200 hover:border-gray-300 focus:border-primary-500 focus:bg-white focus:ring-4 focus:ring-primary-500/15 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:hover:border-gray-500 dark:focus:border-primary-400 dark:focus:bg-gray-700"
                                            data-index="{{ $i }}"
                                            autocomplete="off">
                                    @endfor
                                </div>

                                {{-- Hidden combined input --}}
                                <input type="hidden" name="otp" id="otp-hidden">

                                @error('otp')
                                    <div class="mt-3 flex items-center gap-2 text-sm text-red-600 dark:text-red-400">
                                        <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>

                            {{-- Verify Button --}}
                            <button type="submit" id="verify-btn"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-primary-700 px-4 py-3.5 text-sm font-semibold text-white shadow-lg shadow-primary-900/15 transition-all duration-300 hover:bg-primary-800 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-primary-500/20 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-50">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                {{ __('Verifikasi') }}
                            </button>
                        </form>

                        {{-- Divider --}}
                        <div class="relative my-7">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                            </div>
                        </div>

                        {{-- Resend & Timer --}}
                        <div class="space-y-4 text-center">
                            <div class="flex items-center justify-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ __('Kode berlaku') }} <span id="countdown" class="font-semibold tabular-nums text-primary-600 dark:text-primary-400">10:00</span></span>
                            </div>

                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Tidak menerima kode?') }}
                                <form method="POST" action="{{ route('2fa.resend', $document) }}" class="mt-1 inline">
                                    @csrf
                                    <button type="submit" class="font-semibold text-primary-600 transition-colors hover:text-primary-500 hover:underline dark:text-primary-400 dark:hover:text-primary-300">
                                        {{ __('Kirim ulang OTP') }}
                                    </button>
                                </form>
                            </p>
                        </div>
                    </div>
                </section>
            </div>

            {{-- Back Link --}}
            <div class="mt-5 text-center">
                <a href="{{ route('approvals.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm text-gray-500 transition-colors hover:bg-white hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-900 dark:hover:text-gray-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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