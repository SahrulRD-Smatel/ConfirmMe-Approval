<x-app-layout>
    <x-slot name="title">{{ __('Buat Pengumuman Baru') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('announcements.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Buat Pengumuman Baru') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Susun informasi yang jelas, pilih tingkat urgensi, lalu terbitkan ke seluruh tim.') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto w-full max-w-6xl">
        <x-ui.card class="overflow-hidden rounded-[1.75rem] border-gray-200/80 p-0 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80">
            <form action="{{ route('announcements.store') }}" method="POST" class="space-y-0"
                enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_20rem]">
                    <div class="space-y-7 p-5 sm:p-7 lg:p-8">
                        <div class="border-b border-gray-200/80 pb-5 dark:border-gray-700/80">
                            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-400">{{ __('Message') }}</p>
                            <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Isi pengumuman') }}</h2>
                            <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">{{ __('Gunakan judul yang mudah dipindai dan isi yang langsung pada inti informasi.') }}</p>
                        </div>

                        <div>
                            <x-input-label for="title" value="{{ __('Judul Pengumuman') }}" />
                            <x-text-input id="title" name="title" type="text" class="mt-2 block h-11 w-full rounded-xl" :value="old('title')"
                                required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="content" value="{{ __('Isi Pengumuman') }}" />
                            <textarea id="content" name="content" rows="9"
                                class="mt-2 block w-full rounded-xl border-gray-300 leading-6 shadow-sm focus:border-primary-500 focus:ring-primary-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                required>{{ old('content') }}</textarea>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Gunakan enter untuk paragraf baru.') }}</p>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>
                    </div>

                    <aside class="border-t border-gray-200/80 bg-gray-50/70 p-5 sm:p-7 lg:border-l lg:border-t-0 lg:p-6 dark:border-gray-800/80 dark:bg-gray-800/30">
                        <div class="mb-6">
                            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-400">{{ __('Publishing controls') }}</p>
                            <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Pengaturan terbit') }}</h2>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <x-input-label for="type" value="{{ __('Tipe / Urgensi') }}" />
                                <select name="type" id="type"
                                    class="mt-2 block h-11 w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <option value="info">{{ __('Informasi (Biru)') }}</option>
                                    <option value="warning">{{ __('Peringatan (Kuning)') }}</option>
                                    <option value="urgent">{{ __('Penting (Oranye)') }}</option>
                                    <option value="danger">{{ __('Kritis / Bahaya (Merah)') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="attachments" value="{{ __('Lampiran (Gambar/Dokumen)') }}" />
                                <input id="attachments" name="attachments[]" type="file" multiple
                                    onchange="validateAnnouncementFiles(this)"
                                    class="mt-2 block w-full cursor-pointer rounded-xl border border-gray-300 bg-white text-xs text-gray-900 file:mr-3 file:border-0 file:bg-primary-50 file:px-3 file:py-2.5 file:text-xs file:font-semibold file:text-primary-700 hover:file:bg-primary-100 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-400 dark:file:bg-primary-900/30 dark:file:text-primary-300"
                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                                <p class="mt-2 text-xs leading-5 text-gray-500 dark:text-gray-400">{{ __('Opsional. Bisa pilih lebih dari satu. Maks 10MB/file.') }}</p>
                                <x-input-error :messages="$errors->get('attachments')" class="mt-2" />
                                @foreach($errors->get('attachments.*') as $messages)
                                    @foreach($messages as $message)
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-8 rounded-2xl border border-primary-100 bg-primary-50/70 p-4 dark:border-primary-900/40 dark:bg-primary-900/10">
                            <p class="text-xs font-semibold text-primary-800 dark:text-primary-300">{{ __('Sebelum menerbitkan') }}</p>
                            <p class="mt-1 text-xs leading-5 text-primary-700/80 dark:text-primary-300/70">{{ __('Pastikan urgensi dan isi pesan sudah sesuai dengan audiens pengumuman.') }}</p>
                        </div>
                    </aside>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-200/80 bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-end sm:px-7 dark:border-gray-800/80 dark:bg-gray-900">
                    <x-ui.button href="{{ route('announcements.index') }}" variant="secondary">{{ __('Batal') }}</x-ui.button>
                    <x-ui.button type="submit">{{ __('Terbitkan') }}</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
    @push('scripts')
    <script>
        function validateAnnouncementFiles(input) {
            const maxFileSize = 10 * 1024 * 1024; // 10MB
            if (input.files) {
                for (let i = 0; i < input.files.length; i++) {
                    if (input.files[i].size > maxFileSize) {
                        showAlert('{{ __('Ukuran file') }} "' + input.files[i].name + '" {{ __('melebihi batas 10MB. Silakan pilih file yang lebih kecil.') }}', 'error');
                        input.value = '';
                        return;
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>