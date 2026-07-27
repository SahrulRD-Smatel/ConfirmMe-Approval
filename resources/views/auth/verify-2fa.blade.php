<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">

        {{-- Background decorative elements --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-200/20 dark:bg-primary-800/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-primary-300/20 dark:bg-primary-700/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        </div>

        <div class="max-w-md w-full relative z-10">
            {{-- Main Card --}}
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/50 dark:border-gray-700/50 p-8 space-y-6">

                {{-- Animated Shield Icon --}}
                <div class="flex justify-center">
                    <div class="relative">
                        <div class="absolute inset-0 bg-primary-400/20 rounded-full blur-xl animate-pulse"></div>
                        <div class="relative w-20 h-20 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center shadow-lg shadow-primary-500/30">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        {{-- Pulse ring --}}
                        <div class="absolute inset-0 rounded-full border-2 border-primary-400/40 animate-ping" style="animation-duration: 2s;"></div>
                    </div>
                </div>

                {{-- Header --}}
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white font-display">
                        Verifikasi 2FA
                    </h2>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Masukkan kode OTP 6 digit yang telah dikirim ke email Anda
                    </p>
                </div>

                {{-- Document Info Card --}}
                <div class="bg-gradient-to-r from-primary-50 to-primary-100/50 dark:from-primary-900/20 dark:to-primary-800/10 rounded-xl p-4 border border-primary-100 dark:border-primary-800/30">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-white dark:bg-gray-700 rounded-lg flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-primary-600/70 dark:text-primary-400/70 uppercase tracking-wider">Dokumen yang akan disetujui</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $document->title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $document->document_number }}</p>
                            @if($document->estimated_budget)
                                @php
                                    $financeApproval2fa = $document->approvals()
                                        ->where('approver_role', 'finance')
                                        ->where('status', 'approved')
                                        ->whereNotNull('approved_amount')
                                        ->first();
                                @endphp
                                @if($financeApproval2fa && (float) $financeApproval2fa->approved_amount !== (float) $document->estimated_budget)
                                    <p class="mt-1.5 text-xs text-gray-400 line-through">
                                        Rp {{ number_format($document->estimated_budget, 0, ',', '.') }}
                                    </p>
                                    <p class="text-sm font-bold text-primary-600 dark:text-primary-400">
                                        Rp {{ number_format($financeApproval2fa->approved_amount, 0, ',', '.') }}
                                        <span class="text-xs font-normal text-yellow-600 dark:text-yellow-400">(Disetujui Finance)</span>
                                    </p>
                                @else
                                    <p class="mt-1.5 text-sm font-bold text-primary-600 dark:text-primary-400">
                                        Rp {{ number_format($document->estimated_budget, 0, ',', '.') }}
                                    </p>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Debug OTP Display --}}
                @if(isset($debugOtp) && $debugOtp)
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="flex h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span class="text-xs font-bold text-amber-700 dark:text-amber-300 uppercase tracking-wider">Debug Mode</span>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center border border-amber-100 dark:border-amber-800/30">
                            <span class="text-2xl font-mono font-bold tracking-[0.5em] text-amber-700 dark:text-amber-300">
                                {{ $debugOtp }}
                            </span>
                        </div>
                    </div>
                @endif

                {{-- OTP Form --}}
                <form method="POST" action="{{ route('2fa.verify', $document) }}" class="space-y-5" id="otp-form">
                    @csrf

                    <div>
                        {{-- OTP Individual Boxes --}}
                        <div class="flex justify-center gap-2 sm:gap-3" id="otp-container">
                            @for($i = 0; $i < 6; $i++)
                                <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric"
                                    class="otp-digit w-11 h-14 sm:w-13 sm:h-16 text-center text-xl sm:text-2xl font-bold font-mono border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:focus:ring-primary-400/20 outline-none transition-all duration-200 shadow-sm hover:border-gray-300 dark:hover:border-gray-500"
                                    data-index="{{ $i }}"
                                    autocomplete="off">
                            @endfor
                        </div>

                        {{-- Hidden combined input --}}
                        <input type="hidden" name="otp" id="otp-hidden">

                        @error('otp')
                            <div class="mt-3 flex items-center justify-center gap-2 text-sm text-red-600 dark:text-red-400">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    {{-- Verify Button --}}
                    <button type="submit" id="verify-btn"
                        class="w-full flex items-center justify-center gap-2 py-3.5 px-4 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/25 hover:shadow-primary-500/40 transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Verifikasi
                    </button>
                </form>

                {{-- Divider --}}
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                    </div>
                </div>

                {{-- Resend & Timer --}}
                <div class="text-center space-y-3 pt-2">
                    <div class="flex items-center justify-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Kode berlaku <span id="countdown" class="font-semibold text-primary-600 dark:text-primary-400 tabular-nums">10:00</span></span>
                    </div>

                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Tidak menerima kode?
                        <form method="POST" action="{{ route('2fa.resend', $document) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300 font-semibold hover:underline transition-colors">
                                Kirim ulang OTP
                            </button>
                        </form>
                    </p>
                </div>
            </div>

            {{-- Back Link --}}
            <div class="text-center mt-6">
                <a href="{{ route('approvals.index') }}"
                    class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke daftar approval
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
                    countdownEl.textContent = 'Kadaluarsa';
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