<x-app-layout>
    <x-slot name="title">{{ __('Buat Purchase Order Baru') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('finance.po.index') }}"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Buat Purchase Order Baru') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Buat PO dari dokumen yang sudah disetujui') }}
                </p>
            </div>
        </div>
    </x-slot>

    <form action="{{ route('finance.po.store') }}" method="POST" x-data="poForm(@js($documents->keyBy('id')))">
        @csrf

        @if($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl">
                <strong class="font-bold">{{ __('Error!') }}</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Document Selection -->
                <x-ui.card>
                    <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Dokumen Referensi') }}</h2>

                    <div>
                        <label for="document_id"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Pilih Dokumen') }} <span class="text-red-500">*</span>
                        </label>
                        <select name="document_id" id="document_id" required x-model="document_id" @change="loadDocumentData(true)"
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
                            <input type="text" name="vendor_name" id="vendor_name" x-model="vendor_name"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10">
                        </div>

                        <div class="md:col-span-2">
                            <label for="vendor_address"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Alamat Vendor') }}
                            </label>
                            <textarea name="vendor_address" id="vendor_address" rows="2" x-model="vendor_address"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10"></textarea>
                        </div>

                        <div>
                            <label for="vendor_phone"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Telepon') }}
                            </label>
                            <input type="text" name="vendor_phone" id="vendor_phone" x-model="vendor_phone"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10">
                        </div>

                        <div>
                            <label for="vendor_email"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Email') }}
                            </label>
                            <input type="email" name="vendor_email" id="vendor_email" x-model="vendor_email"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10">
                        </div>
                    </div>
                </x-ui.card>

                <!-- Order Details -->
                <x-ui.card>
                    <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Detail Pesanan') }}</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="order_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Tanggal Order') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="order_date" id="order_date"
                                value="{{ old('order_date', date('Y-m-d')) }}" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10">
                        </div>

                        <div>
                            <label for="expected_delivery_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Estimasi Pengiriman') }}
                            </label>
                            <input type="date" name="expected_delivery_date" id="expected_delivery_date"
                                value="{{ old('expected_delivery_date') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10">
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="border-t pt-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">{{ __('Item Pesanan') }}</h3>

                        <template x-for="(item, index) in items" :key="index">
                            <div class="grid grid-cols-12 gap-2 mb-3 items-start">
                                <div class="col-span-4">
                                    <input type="text" :name="'items['+index+'][item_name]'" x-model="item.name"
                                        required placeholder="{{ __('Nama Item') }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>
                                <div class="col-span-2">
                                    <input type="number" :name="'items['+index+'][quantity]'" x-model="item.qty"
                                        @input="calculateTotal()" required min="1" placeholder="{{ __('Qty') }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>
                                <div class="col-span-2">
                                    <input type="text" :name="'items['+index+'][unit]'" x-model="item.unit" required
                                        placeholder="{{ __('Unit') }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>
                                <div class="col-span-3">
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                                        <input type="text" x-model="item.price_formatted" @input="formatPrice(item)"
                                            required placeholder="0" inputmode="numeric"
                                            class="w-full pl-10 pr-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <input type="hidden" :name="'items['+index+'][unit_price]'" :value="item.price">
                                    </div>
                                </div>
                                <div class="col-span-1 flex justify-center">
                                    <button type="button" @click="removeItem(index)"
                                        class="text-red-500 hover:text-red-700 p-2" x-show="items.length > 1">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </template>

                        <button type="button" @click="addItem()"
                            class="text-primary-600 hover:text-primary-800 text-sm font-medium flex items-center gap-1 mt-2">
                            <x-icon name="plus" class="w-4 h-4" /> {{ __('Tambah Item') }}
                        </button>
                    </div>
                </x-ui.card>

                <!-- Additional Info -->
                <x-ui.card>
                    <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Informasi Tambahan') }}</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="tax_amount"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Pajak') }} (Rp)
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                                <input type="text" id="tax_amount" x-model="tax_formatted" @input="formatTax()"
                                    placeholder="0" inputmode="numeric"
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10">
                                <input type="hidden" name="tax_amount" :value="tax">
                            </div>
                        </div>

                        <div>
                            <label for="terms_conditions"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Syarat & Ketentuan') }}
                            </label>
                            <textarea name="terms_conditions" id="terms_conditions" rows="3" x-model="terms_conditions"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10"></textarea>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Catatan') }}
                            </label>
                            <textarea name="notes" id="notes" rows="2" x-model="notes"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-4 focus:ring-primary-500/20/10"></textarea>
                        </div>
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

                <!-- Summary -->
                <x-ui.card>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ __('Ringkasan') }}</h3>

                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">{{ __('Subtotal') }}</span>
                            <span class="text-gray-900 dark:text-white"
                                x-text="'Rp ' + subtotal.toLocaleString('id-ID')"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">{{ __('Pajak') }}</span>
                            <span class="text-gray-900 dark:text-white"
                                x-text="'Rp ' + parseInt(tax || 0).toLocaleString('id-ID')"></span>
                        </div>
                        <div class="border-t pt-2 flex justify-between">
                            <span class="font-semibold">{{ __('Total') }}</span>
                            <span class="font-bold text-primary-600"
                                x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                        </div>
                    </div>
                </x-ui.card>

                <!-- Actions -->
                <x-ui.card>
                    <div class="flex flex-col gap-3">
                        <x-ui.button type="submit" class="w-full" icon="check">
                            {{ __('Simpan PO') }}
                        </x-ui.button>
                        <x-ui.button href="{{ route('finance.po.index') }}" variant="secondary" class="w-full">
                            {{ __('Batal') }}
                        </x-ui.button>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </form>

    <script>
        function poForm(documents) {
            return {
                document_id: @js(old('document_id', $document?->id ?? '')),
                vendor_name: @js(old('vendor_name')),
                vendor_address: @js(old('vendor_address')),
                vendor_phone: @js(old('vendor_phone')),
                vendor_email: @js(old('vendor_email')),
                notes: @js(old('notes')),
                terms_conditions: @js(old('terms_conditions')),
                
                items: [{ name: '', qty: 1, unit: 'pcs', price: 0, price_formatted: '0' }],
                tax: {{ old('tax_amount', 0) }},
                tax_formatted: '{{ old('tax_amount', 0) }}'.replace(/\B(?=(\d{3})+(?!\d))/g, '.'),
                subtotal: 0,
                total: 0,
                documents: documents || {},

                init() {
                    @if(old('items'))
                        this.items = {!! json_encode(old('items')) !!}.map(i => {
                            let p = parseInt(i.unit_price || 0);
                            return {
                                name: i.item_name || '',
                                qty: parseInt(i.quantity || 1),
                                unit: i.unit || 'pcs',
                                price: p,
                                price_formatted: String(p).replace(/\B(?=(\d{3})+(?!\d))/g, '.')
                            };
                        });
                    @else
                        if (this.document_id) {
                            this.loadDocumentData(true);
                        }
                    @endif
                    this.calculateTotal();
                },

                loadDocumentData(overwrite = true) {
                    if (!this.document_id || !this.documents[this.document_id]) return;
                    
                    let doc = this.documents[this.document_id];
                    let meta = doc.metadata || {};
                    
                    if (overwrite) {
                        this.vendor_name = meta.vendor_name || '';
                        this.vendor_address = meta.vendor_address || '';
                        this.vendor_phone = meta.vendor_phone || '';
                        this.vendor_email = meta.vendor_email || '';
                        this.tax = parseInt(meta.tax_amount || 0);
                        this.tax_formatted = String(this.tax).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        this.notes = meta.keterangan || '';
                        
                        if (meta.items && Array.isArray(meta.items) && meta.items.length > 0) {
                            this.items = meta.items.map(i => {
                                let p = parseInt(i['Harga Perk./Item'] || i['Harga Perkiraan'] || i.price || 0);
                                return {
                                    name: i['Nama Barang'] || i.item_name || i.name || '',
                                    qty: parseInt(i['Permintaan (Qty)'] || i.quantity || i.qty || 1),
                                    unit: i['Unit'] || i.unit || 'pcs',
                                    price: p,
                                    price_formatted: String(p).replace(/\B(?=(\d{3})+(?!\d))/g, '.')
                                };
                            });
                        } else {
                            let p = parseInt(doc.approved_budget || 0);
                            this.items = [{ name: meta.item_description || '', qty: 1, unit: 'pcs', price: p, price_formatted: String(p).replace(/\B(?=(\d{3})+(?!\d))/g, '.') }];
                        }
                    }
                    this.calculateTotal();
                },

                formatPrice(item) {
                    let raw = String(item.price_formatted).replace(/[^\d]/g, '');
                    item.price = parseInt(raw) || 0;
                    item.price_formatted = raw ? raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
                    this.calculateTotal();
                },

                formatTax() {
                    let raw = String(this.tax_formatted).replace(/[^\d]/g, '');
                    this.tax = parseInt(raw) || 0;
                    this.tax_formatted = raw ? raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
                    this.calculateTotal();
                },

                addItem() {
                    this.items.push({ name: '', qty: 1, unit: 'pcs', price: 0, price_formatted: '0' });
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                    this.calculateTotal();
                },

                calculateTotal() {
                    this.subtotal = this.items.reduce((sum, item) => {
                        return sum + (parseInt(item.qty || 0) * parseInt(item.price || 0));
                    }, 0);
                    this.total = this.subtotal + parseInt(this.tax || 0);
                }
            }
        }
    </script>
</x-app-layout>