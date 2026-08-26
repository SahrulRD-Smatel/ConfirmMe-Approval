<x-app-layout>
    <x-slot name="title">{{ __('Ajukan Cuti') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('leave-requests.index') }}"
                class="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 text-gray-500 transition hover:border-primary-200 hover:bg-primary-50 hover:text-primary-700 dark:border-gray-700 dark:hover:border-primary-800 dark:hover:bg-primary-900/20 dark:hover:text-primary-300">
                <x-icon name="arrow-left" class="h-5 w-5" />
            </a>
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    {{ __('Ajukan Cuti') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Isi form berikut untuk mengajukan cuti') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto w-full max-w-5xl">
        <div class="grid grid-cols-1 items-start gap-5 lg:grid-cols-[minmax(0,1fr)_18rem]">
            <div class="space-y-5">
                {{-- Leave Balance Info --}}
                <x-ui.card class="border-primary-100 dark:border-primary-900/40">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400">
                                <x-icon name="calendar" class="h-6 w-6" />
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-[0.12em] text-gray-400">
                                    {{ __('Sisa Cuti Tahunan Anda') }}
                                </p>
                                <p class="mt-1 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                    {{ $leaveBalance }} {{ __('hari') }}
                                </p>
                            </div>
                        </div>
                        <span class="hidden text-right text-xs leading-5 text-gray-400 sm:block">
                            {!! __('Saldo cuti<br>tahun berjalan') !!}
                        </span>
                    </div>
                </x-ui.card>

                <x-ui.card>
                    <form action="{{ route('leave-requests.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf

                        <div class="border-b border-gray-100 pb-5 dark:border-gray-700">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-primary-600 dark:text-primary-400">
                                {{ __('Form pengajuan') }}
                            </p>
                            <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">
                                {{ __('Detail cuti') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Lengkapi informasi berikut sebelum mengirim permintaan.') }}
                            </p>
                        </div>

                        {{-- Leave Type --}}
                        <div>
                            <x-input-label for="type" value="{{ __('Jenis Cuti') }}" />
                            <select id="type" name="type"
                                class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20/10"
                                required>
                                <option value="">{{ __('-- Pilih Jenis Cuti --') }}</option>
                                <option value="annual" {{ old('type') == 'annual' ? 'selected' : '' }}>
                                    {{ __('Cuti Tahunan') }}
                                </option>
                                <option value="sick" {{ old('type') == 'sick' ? 'selected' : '' }}>
                                    {{ __('Sakit') }}
                                </option>
                                <option value="unpaid" {{ old('type') == 'unpaid' ? 'selected' : '' }}>
                                    {{ __('Cuti Tanpa Gaji') }}
                                </option>
                                <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>
                                    {{ __('Lainnya') }}
                                </option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        {{-- Date Range --}}
                        <div>
                            <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('Periode Cuti') }}
                                </p>
                                <span class="text-xs text-gray-400">
                                    {{ __('Pilih tanggal mulai dan selesai') }}
                                </span>
                            </div>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <x-input-label for="start_date" value="{{ __('Tanggal Mulai') }}" />
                                    <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full"
                                        :value="old('start_date')" required min="{{ date('Y-m-d') }}" />
                                    <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="end_date" value="{{ __('Tanggal Selesai') }}" />
                                    <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full"
                                        :value="old('end_date')" required min="{{ date('Y-m-d') }}" />
                                    <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        {{-- Duration Preview --}}
                        <div x-data="{ startDate: '', endDate: '', duration: 0 }" x-init="
                                $watch('startDate', () => { if(startDate && endDate) duration = Math.ceil((new Date(endDate) - new Date(startDate)) / (1000*60*60*24)) + 1 });
                                $watch('endDate', () => { if(startDate && endDate) duration = Math.ceil((new Date(endDate) - new Date(startDate)) / (1000*60*60*24)) + 1 });
                             "
                            class="rounded-2xl border border-gray-200/80 bg-gray-50/80 p-4 dark:border-gray-700/80 dark:bg-gray-800/70">
                            <div class="flex items-center gap-2">
                                <x-icon name="information-circle" class="h-5 w-5 text-gray-400" />
                                <span class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ __('Durasi cuti:') }}
                                    <strong x-text="duration > 0 ? duration + ' ' + '{{ __('hari') }}' : '{{ __('Pilih tanggal') }}'"></strong>
                                </span>
                            </div>
                            <script>
                                document.getElementById('start_date').addEventListener('change', (e) => {
                                    Alpine.store('leaveForm', { startDate: e.target.value });
                                });
                            </script>
                        </div>

                        {{-- Reason --}}
                        <div>
                            <x-input-label for="reason" value="{{ __('Alasan') }}" />
                            <textarea id="reason" name="reason" rows="3"
                                class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20/10"
                                placeholder="{{ __('Jelaskan alasan pengajuan cuti Anda...') }}" required>{{ old('reason') }}</textarea>
                            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                        </div>

                        {{-- Attachment --}}
                        <div>
                            <x-input-label for="attachment" value="{{ __('Lampiran (Opsional)') }}" />
                            <p class="mb-2 mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">
                                {{ __('Untuk cuti sakit, lampirkan surat dokter. Format: PDF, JPG, PNG (Max 5MB)') }}
                            </p>
                            <input type="file" id="attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png"
                                class="mt-1 block w-full rounded-xl border border-dashed border-gray-300 bg-gray-50 p-2 text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-400 dark:file:bg-primary-900 dark:file:text-primary-300" />
                            <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
                        </div>

                        {{-- Submit --}}
                        <div class="flex flex-wrap items-center justify-end gap-3 border-t border-gray-200/80 pt-5 dark:border-gray-700/80">
                            <a href="{{ route('leave-requests.index') }}"
                                class="rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                Batal
                            </a>
                            <x-ui.button type="submit" icon="paper-airplane">
                                {{ __('Ajukan Cuti') }}
                            </x-ui.button>
                        </div>
                    </form>
                </x-ui.card>
            </div>

            <aside class="space-y-4 lg:sticky lg:top-24">
                <div class="rounded-2xl border border-primary-100 bg-primary-50/70 p-5 dark:border-primary-900/40 dark:bg-primary-900/20">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-primary-600 shadow-sm dark:bg-gray-800 dark:text-primary-400">
                        <x-icon name="paper-airplane" class="h-5 w-5" />
                    </div>
                    <h2 class="mt-4 text-base font-semibold text-gray-900 dark:text-white">
                        {{ __('Pengajuan cuti') }}
                    </h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        {{ __('Pastikan informasi yang Anda masukkan sudah benar sebelum dikirim untuk diproses.') }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Checklist
                    </h2>
                    <div class="mt-4 space-y-3">
                        <div class="flex gap-3 text-sm text-gray-600 dark:text-gray-300">
                            <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-primary-500"></span>
                            <span>{{ __('Periksa kembali periode cuti.') }}</span>
                        </div>
                        <div class="flex gap-3 text-sm text-gray-600 dark:text-gray-300">
                            <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-primary-500"></span>
                            <span>{{ __('Gunakan alasan yang jelas.') }}</span>
                        </div>
                        <div class="flex gap-3 text-sm text-gray-600 dark:text-gray-300">
                            <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-primary-500"></span>
                            <span>{{ __('Lampirkan dokumen jika diperlukan.') }}</span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>