<section class="space-y-5">
    <header>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
            Informasi Rekening Bank
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Lengkapi informasi rekening bank Anda untuk keperluan pencairan pinjaman dan reimbursement.
        </p>
    </header>

    <form method="post" action="{{ route('profile.bank.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="bank_name" :value="__('Nama Bank')" />
            <select id="bank_name" name="bank_name"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 rounded-xl shadow-sm">
                <option value="">-- Pilih Bank --</option>
                <option value="BCA" {{ old('bank_name', $user->bank_name) == 'BCA' ? 'selected' : '' }}>BCA</option>
                <option value="BNI" {{ old('bank_name', $user->bank_name) == 'BNI' ? 'selected' : '' }}>BNI</option>
                <option value="BRI" {{ old('bank_name', $user->bank_name) == 'BRI' ? 'selected' : '' }}>BRI</option>
                <option value="Mandiri" {{ old('bank_name', $user->bank_name) == 'Mandiri' ? 'selected' : '' }}>Mandiri
                </option>
                <option value="CIMB Niaga" {{ old('bank_name', $user->bank_name) == 'CIMB Niaga' ? 'selected' : '' }}>CIMB
                    Niaga</option>
                <option value="Danamon" {{ old('bank_name', $user->bank_name) == 'Danamon' ? 'selected' : '' }}>Danamon
                </option>
                <option value="Permata" {{ old('bank_name', $user->bank_name) == 'Permata' ? 'selected' : '' }}>Permata
                </option>
                <option value="BTN" {{ old('bank_name', $user->bank_name) == 'BTN' ? 'selected' : '' }}>BTN</option>
                <option value="OCBC NISP" {{ old('bank_name', $user->bank_name) == 'OCBC NISP' ? 'selected' : '' }}>OCBC
                    NISP</option>
                <option value="Panin Bank" {{ old('bank_name', $user->bank_name) == 'Panin Bank' ? 'selected' : '' }}>
                    Panin Bank</option>
                <option value="Bank Jago" {{ old('bank_name', $user->bank_name) == 'Bank Jago' ? 'selected' : '' }}>Bank
                    Jago</option>
                <option value="Seabank" {{ old('bank_name', $user->bank_name) == 'Seabank' ? 'selected' : '' }}>Seabank
                </option>
                <option value="Lainnya" {{ old('bank_name', $user->bank_name) == 'Lainnya' ? 'selected' : '' }}>Lainnya
                </option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('bank_name')" />
        </div>

        <div>
            <x-input-label for="bank_account_number" :value="__('Nomor Rekening')" />
            <x-text-input id="bank_account_number" name="bank_account_number" type="text" class="mt-1 block w-full"
                :value="old('bank_account_number', $user->bank_account_number)" placeholder="Contoh: 1234567890" />
            <x-input-error class="mt-2" :messages="$errors->get('bank_account_number')" />
        </div>

        <div>
            <x-input-label for="bank_account_name" :value="__('Nama Pemilik Rekening')" />
            <x-text-input id="bank_account_name" name="bank_account_name" type="text" class="mt-1 block w-full"
                :value="old('bank_account_name', $user->bank_account_name)" placeholder="Nama sesuai buku tabungan" />
            <x-input-error class="mt-2" :messages="$errors->get('bank_account_name')" />
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <x-ui.button variant="primary" type="submit">{{ __('Simpan') }}</x-ui.button>

            @if (session('status') === 'bank-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 dark:text-green-400">{{ __('Tersimpan.') }}</p>
            @endif
        </div>
    </form>
</section>