<x-app-layout>
    <x-slot name="title">Ajukan Cuti</x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('leave-requests.index') }}"
                class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Ajukan Cuti</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Isi form berikut untuk mengajukan cuti</p>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto w-full max-w-3xl">
        {{-- Leave Balance Info --}}
        <x-ui.card class="mb-7">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 p-3 bg-primary-100 dark:bg-primary-900 rounded-full">
                    <x-icon name="calendar" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sisa Cuti Tahunan Anda</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $leaveBalance }} hari</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <form action="{{ route('leave-requests.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf

                {{-- Leave Type --}}
                <div>
                    <x-input-label for="type" value="Jenis Cuti" />
                    <select id="type" name="type"
                        class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20/10"
                        required>
                        <option value="">-- Pilih Jenis Cuti --</option>
                        <option value="annual" {{ old('type') == 'annual' ? 'selected' : '' }}>Cuti Tahunan</option>
                        <option value="sick" {{ old('type') == 'sick' ? 'selected' : '' }}>Sakit</option>
                        <option value="unpaid" {{ old('type') == 'unpaid' ? 'selected' : '' }}>Cuti Tanpa Gaji</option>
                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>

                {{-- Date Range --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="start_date" value="Tanggal Mulai" />
                        <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full"
                            :value="old('start_date')" required min="{{ date('Y-m-d') }}" />
                        <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="end_date" value="Tanggal Selesai" />
                        <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full"
                            :value="old('end_date')" required min="{{ date('Y-m-d') }}" />
                        <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                    </div>
                </div>

                {{-- Duration Preview --}}
                <div x-data="{ startDate: '', endDate: '', duration: 0 }" x-init="
                        $watch('startDate', () => { if(startDate && endDate) duration = Math.ceil((new Date(endDate) - new Date(startDate)) / (1000*60*60*24)) + 1 });
                        $watch('endDate', () => { if(startDate && endDate) duration = Math.ceil((new Date(endDate) - new Date(startDate)) / (1000*60*60*24)) + 1 });
                     " class="rounded-2xl border border-gray-200/80 bg-gray-50/80 p-4 dark:border-gray-700/80 dark:bg-gray-800/70">
                    <div class="flex items-center gap-2">
                        <x-icon name="information-circle" class="w-5 h-5 text-gray-400" />
                        <span class="text-sm text-gray-600 dark:text-gray-300">
                            Durasi cuti: <strong x-text="duration > 0 ? duration + ' hari' : 'Pilih tanggal'"></strong>
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
                    <x-input-label for="reason" value="Alasan" />
                    <textarea id="reason" name="reason" rows="3"
                        class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20/10"
                        placeholder="Jelaskan alasan pengajuan cuti Anda..." required>{{ old('reason') }}</textarea>
                    <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                </div>

                {{-- Attachment --}}
                <div>
                    <x-input-label for="attachment" value="Lampiran (Opsional)" />
                    <p class="text-xs text-gray-500 mb-2">Untuk cuti sakit, lampirkan surat dokter. Format: PDF, JPG,
                        PNG (Max 5MB)</p>
                    <input type="file" id="attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900 dark:file:text-primary-300" />
                    <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
                </div>

                {{-- Submit --}}
                <div class="flex flex-wrap items-center justify-end gap-3 border-t border-gray-200/80 pt-5 dark:border-gray-700/80">
                    <a href="{{ route('leave-requests.index') }}"
                        class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        Batal
                    </a>
                    <x-ui.button type="submit" icon="paper-airplane">Ajukan Cuti</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>