<x-app-layout>
    <x-slot name="title">{{ __('Buat Invoice Baru') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('finance.invoices.index') }}"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Buat Invoice Baru') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Buat invoice dari dokumen yang sudah disetujui') }}
                </p>
            </div>
        </div>
    </x-slot>

    <form action="{{ route('finance.invoices.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Document Selection -->
                <x-ui.card>
                    <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Dokumen Referensi') }}</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="document_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Pilih Dokumen') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="document_id" id="document_id" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10">
                                <option value="">-- {{ __('Pilih Dokumen') }} --</option>
                                @foreach($documents as $doc)
                                    <option value="{{ $doc->id }}" {{ (isset($document) && $document->id == $doc->id) || old('document_id') == $doc->id ? 'selected' : '' }}>
                                        {{ $doc->document_number }} - {{ $doc->title }}
                                        @if($doc->approved_budget)
                                            (Rp {{ number_format($doc->approved_budget, 0, ',', '.') }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('document_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-ui.card>

                <!-- Vendor Information -->
                <x-ui.card>
                    <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Informasi Vendor') }}</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="vendor_name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Nama Vendor') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="vendor_name" id="vendor_name" value="{{ old('vendor_name') }}"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10"
                                placeholder="{{ __('Nama perusahaan vendor') }}">
                            @error('vendor_name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="vendor_address"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Alamat Vendor') }}
                            </label>
                            <textarea name="vendor_address" id="vendor_address" rows="2"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10"
                                placeholder="{{ __('Alamat lengkap vendor') }}">{{ old('vendor_address') }}</textarea>
                        </div>
                    </div>
                </x-ui.card>

                <!-- Invoice Details -->
                <x-ui.card>
                    <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Detail Invoice') }}</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="invoice_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Tanggal Invoice') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="invoice_date" id="invoice_date"
                                value="{{ old('invoice_date', date('Y-m-d')) }}" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10">
                            @error('invoice_date')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="due_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Jatuh Tempo') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10">
                            @error('due_date')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="subtotal"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Subtotal') }} (Rp) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                                <input type="text" name="subtotal" id="subtotal"
                                    value="{{ old('subtotal', isset($document) ? $document->approved_budget : '') }}"
                                    required
                                    class="currency-input w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10"
                                    inputmode="numeric" placeholder="0">
                            </div>
                            @error('subtotal')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tax_amount"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Pajak') }} (Rp)
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                                <input type="text" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', 0) }}"
                                    class="currency-input w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10"
                                    inputmode="numeric" placeholder="0">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Catatan') }}
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10"
                            placeholder="{{ __('Catatan tambahan...') }}">{{ old('notes') }}</textarea>
                    </div>
                </x-ui.card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-7">
                <!-- Document Info -->
                @if(isset($document))
                    <x-ui.card>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('Dokumen Terpilih') }}</h3>
                        <div class="space-y-2 text-sm">
                            <p><span class="text-gray-500">{{ __('Nomor') }}:</span> <strong>{{ $document->document_number }}</strong>
                            </p>
                            <p><span class="text-gray-500">{{ __('Judul') }}:</span> {{ $document->title }}</p>
                            @if($document->approved_budget)
                                <p><span class="text-gray-500">{{ __('Budget') }}:</span> <strong class="text-green-600">Rp
                                        {{ number_format($document->approved_budget, 0, ',', '.') }}</strong></p>
                            @endif
                        </div>
                    </x-ui.card>
                @endif

                <!-- Actions -->
                <x-ui.card>
                    <div class="flex flex-col gap-3">
                        <x-ui.button type="submit" class="w-full" icon="check">
                            {{ __('Simpan Invoice') }}
                        </x-ui.button>
                        <x-ui.button href="{{ route('finance.invoices.index') }}" variant="secondary" class="w-full">
                            {{ __('Batal') }}
                        </x-ui.button>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </form>
</x-app-layout>