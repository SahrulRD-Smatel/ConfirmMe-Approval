<x-app-layout>
    <x-slot name="title">Buat Pengumuman Baru</x-slot>

    <div class="mx-auto w-full max-w-3xl">
        <x-ui.card>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Buat Pengumuman Baru</h2>

            <form action="{{ route('announcements.store') }}" method="POST" class="space-y-6"
                enctype="multipart/form-data">
                @csrf

                <div>
                    <x-input-label for="title" value="Judul Pengumuman" />
                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')"
                        required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="type" value="Tipe / Urgensi" />
                    <select name="type" id="type"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-soft">
                        <option value="info">Informasi (Biru)</option>
                        <option value="warning">Peringatan (Kuning)</option>
                        <option value="urgent">Penting (Oranye)</option>
                        <option value="danger">Kritis / Bahaya (Merah)</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="attachment" value="Lampiran (Gambar/Dokumen)" />
                    <input id="attachment" name="attachment" type="file"
                        class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-xl cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                    <p class="mt-1 text-xs text-gray-500">Opsional. Maks 10MB.</p>
                    <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="content" value="Isi Pengumuman" />
                    <textarea id="content" name="content" rows="6"
                        class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-soft mt-1"
                        required>{{ old('content') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Gunakan enter untuk paragraf baru.</p>
                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3">
                    <x-ui.button href="{{ route('announcements.index') }}" variant="secondary">Batal</x-ui.button>
                    <x-ui.button type="submit">Terbitkan</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>