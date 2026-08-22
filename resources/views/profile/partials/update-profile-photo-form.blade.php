@php
    $user = Auth::user();
@endphp

<section class="space-y-1">
    <header>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            {{ __('Foto Profil') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Unggah foto profil Anda. Format yang didukung: JPG, PNG, GIF. Maksimal 2MB.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.photo.update') }}" class="mt-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="flex flex-col items-start gap-5 sm:flex-row sm:items-center">
            <!-- Current Photo Preview -->
            <div class="shrink-0">
                <div class="relative">
                    @if($user->avatar)
                        <img id="avatar-preview"
                            class="h-24 w-24 object-cover rounded-full border-4 border-gray-200 dark:border-gray-700 shadow-lg"
                            src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                    @else
                        <div id="avatar-placeholder"
                            class="h-24 w-24 rounded-full border-4 border-gray-200 dark:border-gray-700 shadow-lg bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center">
                            <span class="text-3xl font-bold text-gray-500 dark:text-gray-400">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        </div>
                        <img id="avatar-preview"
                            class="h-24 w-24 object-cover rounded-full border-4 border-gray-200 dark:border-gray-700 shadow-lg hidden"
                            src="" alt="Preview">
                    @endif

                    <!-- Camera overlay on hover -->
                    <label for="avatar"
                        class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded-full opacity-0 hover:opacity-100 transition-opacity cursor-pointer">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </label>
                </div>
            </div>

            <!-- Upload Controls -->
            <div class="flex-1">
                <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/gif" class="block w-full text-sm text-gray-500 dark:text-gray-400
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-lg file:border-0
                              file:text-sm file:font-semibold
                              file:bg-primary-50 file:text-primary-700
                              dark:file:bg-primary-900 dark:file:text-primary-300
                              hover:file:bg-primary-100 dark:hover:file:bg-primary-800
                              file:cursor-pointer file:transition-colors" onchange="previewImage(this)">

                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />

                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Klik foto atau pilih file untuk mengganti') }}
                </p>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <x-ui.button variant="primary" type="submit">{{ __('Simpan Foto') }}</x-ui.button>

            @if (session('status') === 'photo-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-green-600 dark:text-green-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ __('Foto berhasil diperbarui.') }}
                </p>
            @endif
        </div>
    </form>

    @if($user->avatar)
        <form method="post" action="{{ route('profile.photo.destroy') }}" class="mt-4" onsubmit="confirmAction(event, 'Apakah Anda yakin ingin menghapus foto profil?')">
            @csrf
            @method('delete')
            <button type="submit"
                class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 hover:underline flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                {{ __('Hapus Foto') }}
            </button>
        </form>
    @endif
</section>

<script>
    function previewImage(input) {
        const preview = document.getElementById('avatar-preview');
        const placeholder = document.getElementById('avatar-placeholder');
        const maxFileSize = 1048576; // 1MB

        if (input.files && input.files[0]) {
            if (input.files[0].size > maxFileSize) {
                showAlert('Ukuran file tidak boleh lebih dari 1MB.', 'error');
                input.value = ''; // Clear the input
                
                // Reset tampilan ke semula
                if (placeholder) {
                    preview.classList.add('hidden');
                    placeholder.classList.remove('hidden');
                } else {
                    preview.src = "{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}";
                }
                return;
            }

            const reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>