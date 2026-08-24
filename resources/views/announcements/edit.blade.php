<x-app-layout>
    <x-slot name="title">{{ __('Edit Pengumuman') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('announcements.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Edit Pengumuman') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Perbarui isi, visibilitas, urgensi, atau lampiran tanpa kehilangan struktur pesan.') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto w-full max-w-6xl">
        <x-ui.card class="overflow-hidden rounded-[1.75rem] border-gray-200/80 p-0 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80">
            <form action="{{ route('announcements.update', $announcement) }}" method="POST" class="space-y-0"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_21rem]">
                    <div class="space-y-7 p-5 sm:p-7 lg:p-8">
                        <div class="border-b border-gray-200/80 pb-5 dark:border-gray-700/80">
                            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-400">{{ __('Message editor') }}</p>
                            <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Isi pengumuman') }}</h2>
                            <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">{{ __('Pertahankan pesan tetap ringkas, mudah dipindai, dan relevan bagi pembaca.') }}</p>
                        </div>

                        <div>
                            <x-input-label for="title" value="{{ __('Judul Pengumuman') }}" />
                            <x-text-input id="title" name="title" type="text" class="mt-2 block h-11 w-full rounded-xl" :value="old('title', $announcement->title)" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="content" value="{{ __('Isi Pengumuman') }}" />
                            <textarea id="content" name="content" rows="11"
                                class="mt-2 block w-full rounded-xl border-gray-300 leading-6 shadow-sm focus:border-primary-500 focus:ring-primary-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                required>{{ old('content', $announcement->content) }}</textarea>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Gunakan enter untuk paragraf baru.') }}</p>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>
                    </div>

                    <aside class="border-t border-gray-200/80 bg-gray-50/70 p-5 sm:p-7 lg:border-l lg:border-t-0 lg:p-6 dark:border-gray-800/80 dark:bg-gray-800/30">
                        <div class="mb-6">
                            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-400">{{ __('Publishing controls') }}</p>
                            <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Pengaturan terbit') }}</h2>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <x-input-label for="type" value="{{ __('Tipe / Urgensi') }}" />
                                <select name="type" id="type"
                                    class="mt-2 block h-11 w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <option value="info" {{ $announcement->type == 'info' ? 'selected' : '' }}>{{ __('Informasi (Biru)') }}
                                    </option>
                                    <option value="warning" {{ $announcement->type == 'warning' ? 'selected' : '' }}>{{ __('Peringatan (Kuning)') }}</option>
                                    <option value="urgent" {{ $announcement->type == 'urgent' ? 'selected' : '' }}>{{ __('Penting (Oranye)') }}
                                    </option>
                                    <option value="danger" {{ $announcement->type == 'danger' ? 'selected' : '' }}>{{ __('Kritis / Bahaya (Merah)') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="is_active" value="{{ __('Status') }}" />
                                <select name="is_active" id="is_active"
                                    class="mt-2 block h-11 w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <option value="1" {{ $announcement->is_active ? 'selected' : '' }}>{{ __('Active (Tampil di Dashboard)') }}
                                    </option>
                                    <option value="0" {{ !$announcement->is_active ? 'selected' : '' }}>{{ __('Archived (Sembunyikan)') }}
                                    </option>
                                </select>
                            </div>

                            <div class="border-t border-gray-200/80 pt-5 dark:border-gray-700/80">
                                <x-input-label value="{{ __('Lampiran Saat Ini') }}" />
                                @if($announcement->attachments->count() > 0)
                                    <div class="mt-3 max-h-56 space-y-2 overflow-y-auto pr-1">
                                        @foreach($announcement->attachments as $attachment)
                                            <div class="rounded-xl border border-gray-200/80 bg-white p-3 dark:border-gray-700 dark:bg-gray-900">
                                                <div class="flex min-w-0 items-center gap-2">
                                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                                                        @if($attachment->file_type === 'image')
                                                            <x-icon name="photograph" class="h-4 w-4 text-purple-500" />
                                                        @else
                                                            <x-icon name="document-text" class="h-4 w-4 text-blue-500" />
                                                        @endif
                                                    </span>
                                                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank"
                                                        class="min-w-0 truncate text-xs font-semibold text-blue-600 hover:underline">
                                                        {{ $attachment->original_name }}
                                                    </a>
                                                </div>
                                                <label class="mt-3 flex cursor-pointer items-center gap-2 border-t border-gray-100 pt-2 text-xs font-semibold text-red-600 hover:text-red-700 dark:border-gray-800 dark:text-red-400">
                                                    <input type="checkbox" name="delete_attachments[]" value="{{ $attachment->id }}"
                                                        class="h-4 w-4 rounded border-gray-300 text-red-600 shadow-soft focus:ring-red-500">
                                                    <span>{{ __('Hapus lampiran ini') }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="mt-2 rounded-xl border border-dashed border-gray-300 px-3 py-3 text-xs text-gray-500 dark:border-gray-600 dark:text-gray-400">{{ __('Tidak ada lampiran.') }}</p>
                                @endif
                            </div>

                            <div class="border-t border-gray-200/80 pt-5 dark:border-gray-700/80">
                                <x-input-label for="attachments" value="{{ __('Tambah Lampiran Baru') }}" />
                                <input id="attachments" name="attachments[]" type="file" multiple
                                    onchange="validateAnnouncementFiles(this)"
                                    class="mt-2 block w-full cursor-pointer rounded-xl border border-gray-300 bg-white text-xs text-gray-900 file:mr-3 file:border-0 file:bg-primary-50 file:px-3 file:py-2.5 file:text-xs file:font-semibold file:text-primary-700 hover:file:bg-primary-100 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-400 dark:file:bg-primary-900/30 dark:file:text-primary-300"
                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                                <p class="mt-2 text-xs leading-5 text-gray-500 dark:text-gray-400">{{ __('File baru akan ditambahkan ke daftar lampiran. Maks 10MB per file.') }}</p>
                                <x-input-error :messages="$errors->get('attachments')" class="mt-2" />
                                @foreach($errors->get('attachments.*') as $messages)
                                    @foreach($messages as $message)
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </aside>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-200/80 bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-end sm:px-7 dark:border-gray-800/80 dark:bg-gray-900">
                    <x-ui.button href="{{ route('announcements.index') }}" variant="secondary">{{ __('Batal') }}</x-ui.button>
                    <x-ui.button type="submit">{{ __('Simpan Perubahan') }}</x-ui.button>
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