<x-app-layout>
    <x-slot name="title">Edit Pengumuman</x-slot>

    <div class="mx-auto w-full max-w-3xl">
        <x-ui.card>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Edit Pengumuman</h2>

            <form action="{{ route('announcements.update', $announcement) }}" method="POST" class="space-y-7"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="title" value="Judul Pengumuman" />
                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $announcement->title)" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="type" value="Tipe / Urgensi" />
                    <select name="type" id="type"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-soft">
                        <option value="info" {{ $announcement->type == 'info' ? 'selected' : '' }}>Informasi (Biru)
                        </option>
                        <option value="warning" {{ $announcement->type == 'warning' ? 'selected' : '' }}>Peringatan
                            (Kuning)</option>
                        <option value="urgent" {{ $announcement->type == 'urgent' ? 'selected' : '' }}>Penting (Oranye)
                        </option>
                        <option value="danger" {{ $announcement->type == 'danger' ? 'selected' : '' }}>Kritis / Bahaya
                            (Merah)</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="is_active" value="Status" />
                    <select name="is_active" id="is_active"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-soft">
                        <option value="1" {{ $announcement->is_active ? 'selected' : '' }}>Active (Tampil di Dashboard)
                        </option>
                        <option value="0" {{ !$announcement->is_active ? 'selected' : '' }}>Archived (Sembunyikan)
                        </option>
                    </select>
                </div>

                <div>
                    <x-input-label value="Lampiran Saat Ini" />
                    @if($announcement->attachments->count() > 0)
                        <div class="mt-2 grid grid-cols-1 gap-2">
                            @foreach($announcement->attachments as $attachment)
                                <div class="flex items-center justify-between p-2 border rounded bg-gray-50 dark:bg-gray-700">
                                    <div class="flex items-center gap-2 overflow-hidden">
                                        @if($attachment->file_type === 'image')
                                            <x-icon name="photograph" class="w-4 h-4 text-purple-500" />
                                        @else
                                            <x-icon name="document-text" class="w-4 h-4 text-blue-500" />
                                        @endif
                                        <a href="{{ Storage::url($attachment->file_path) }}" target="_blank"
                                            class="text-sm text-blue-600 hover:underline truncate">
                                            {{ $attachment->original_name }}
                                        </a>
                                    </div>
                                    <label
                                        class="flex items-center space-x-2 text-xs text-red-600 cursor-pointer hover:bg-red-50 dark:hover:bg-red-900/20 p-1 rounded">
                                        <input type="checkbox" name="delete_attachments[]" value="{{ $attachment->id }}"
                                            class="rounded border-gray-300 text-red-600 shadow-soft focus:ring-red-500">
                                        <span>Hapus</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 mt-1">Tidak ada lampiran.</p>
                    @endif
                </div>

                <div>
                    <x-input-label for="attachments" value="Tambah Lampiran Baru" />
                    <input id="attachments" name="attachments[]" type="file" multiple
                        class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-xl cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                    <p class="mt-1 text-xs text-gray-500">File baru akan ditambahkan ke daftar lampiran. Maks 10MB per
                        file.</p>
                    <x-input-error :messages="$errors->get('attachments.*')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="content" value="Isi Pengumuman" />
                    <textarea id="content" name="content" rows="6"
                        class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-soft mt-1"
                        required>{{ old('content', $announcement->content) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Gunakan enter untuk paragraf baru.</p>
                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3">
                    <x-ui.button href="{{ route('announcements.index') }}" variant="secondary">Batal</x-ui.button>
                    <x-ui.button type="submit">Simpan Perubahan</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>