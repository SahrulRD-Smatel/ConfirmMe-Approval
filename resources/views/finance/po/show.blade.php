<x-app-layout>
    <x-slot name="title">{{ $po->po_number }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('finance.po.index') }}"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                </a>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                            {{ $po->po_number }}
                        </h1>
                        <x-ui.badge :status="match($po->status) { 'issued' => 'success', 'completed' => 'completed', 'cancelled' => 'danger', default => 'warning' }">
                            {{ match($po->status) { 'draft' => 'Draft', 'issued' => 'Issued', 'completed' => 'Completed', 'cancelled' => 'Cancelled', default => ucfirst($po->status) } }}
                        </x-ui.badge>
                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $po->vendor_name }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if($po->status === 'draft')
                    <form action="{{ route('finance.po.issue', $po) }}" method="POST" class="inline">
                        @csrf
                        <x-ui.button type="submit" variant="success" icon="check-circle" size="sm">
                            Terbitkan PO
                        </x-ui.button>
                    </form>
                    <x-ui.button href="{{ route('finance.po.edit', $po) }}" variant="secondary" icon="pencil" size="sm">
                        Edit
                    </x-ui.button>
                @endif
                
                @if($po->status === 'issued')
                    @if($po->invoices()->exists())
                        <x-ui.button href="{{ route('finance.invoices.show', $po->invoices->first()) }}" variant="primary" icon="document-text" size="sm">
                            Lihat Invoice
                        </x-ui.button>
                    @else
                        <form action="{{ route('finance.invoices.from-po', $po) }}" method="POST" class="inline">
                            @csrf
                            <x-ui.button type="submit" variant="primary" icon="document-plus" size="sm">
                                Buat Invoice
                            </x-ui.button>
                        </form>
                    @endif
                @endif
                
                @if($po->status === 'completed' && $po->invoices()->exists())
                    <x-ui.button href="{{ route('finance.invoices.show', $po->invoices->first()) }}" variant="secondary" icon="document-text" size="sm">
                        Lihat Invoice
                    </x-ui.button>
                @endif
                
                <x-ui.button href="{{ route('finance.po.print', $po) }}" target="_blank" variant="secondary"
                    icon="printer" size="sm">
                    Cetak
                </x-ui.button>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- PO Info -->
            <x-ui.card>
                <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Informasi Purchase Order</h2>

                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Nomor PO</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $po->po_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Tanggal Order</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $po->order_date->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Vendor</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $po->vendor_name }}</dd>
                    </div>
                    @if($po->expected_delivery_date)
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Estimasi Pengiriman</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $po->expected_delivery_date->format('d M Y') }}</dd>
                        </div>
                    @endif
                </dl>

                @if($po->vendor_address)
                    <div class="mt-4 pt-4 border-t">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Alamat Vendor</dt>
                        <dd class="text-sm text-gray-900 dark:text-white mt-1">{{ $po->vendor_address }}</dd>
                    </div>
                @endif
            </x-ui.card>

            <!-- Items -->
            <x-ui.card>
                <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Item Pesanan</h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50/80 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Unit</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga
                                    Satuan</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200/80 dark:divide-gray-700/80">
                            @foreach($po->items as $item)
                                <tr>
                                    <td class="px-4 py-3 text-gray-900 dark:text-white">
                                        <div>{{ $item->item_name }}</div>
                                        @if($item->description)
                                            <div class="text-xs text-gray-500">{{ $item->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-900 dark:text-white">{{ $item->quantity }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-500">{{ $item->unit }}</td>
                                    <td class="px-4 py-3 text-right text-gray-900 dark:text-white">Rp
                                        {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">Rp
                                        {{ number_format($item->total_price, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50/80 dark:bg-gray-800">
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-right text-gray-500">Subtotal</td>
                                <td class="px-4 py-2 text-right font-medium text-gray-900 dark:text-white">Rp
                                    {{ number_format($po->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-right text-gray-500">Pajak</td>
                                <td class="px-4 py-2 text-right text-gray-900 dark:text-white">Rp
                                    {{ number_format($po->tax_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="border-t-2 border-gray-300">
                                <td colspan="4"
                                    class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">Total</td>
                                <td class="px-4 py-3 text-right font-bold text-lg text-primary-600">Rp
                                    {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-ui.card>

            <!-- Document Reference -->
            @if($po->document)
                <x-ui.card>
                    <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Dokumen Referensi</h2>

                    <div class="flex items-center justify-between p-4 bg-gray-50/80 dark:bg-gray-800 rounded-xl">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $po->document->document_number }}</p>
                            <p class="text-sm text-gray-500">{{ $po->document->title }}</p>
                        </div>
                        <a href="{{ route('documents.show', $po->document) }}"
                            class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                            Lihat Dokumen →
                        </a>
                    </div>
                </x-ui.card>
            @endif

            <!-- Terms & Notes -->
            @if($po->terms_conditions || $po->notes)
                <x-ui.card>
                    @if($po->terms_conditions)
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Syarat & Ketentuan</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $po->terms_conditions }}
                            </p>
                        </div>
                    @endif
                    @if($po->notes)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Catatan</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $po->notes }}</p>
                        </div>
                    @endif
                </x-ui.card>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status -->
            <x-ui.card>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Status PO</h3>

                <div class="text-center py-4">
                    @if($po->status === 'issued')
                        <x-icon name="check-circle" class="w-12 h-12 text-green-500 mx-auto mb-2" />
                        <p class="text-green-600 font-semibold">TERBIT</p>
                    @elseif($po->status === 'completed')
                        <x-icon name="check-circle" class="w-12 h-12 text-blue-500 mx-auto mb-2" />
                        <p class="text-blue-600 font-semibold">SELESAI</p>
                    @elseif($po->status === 'cancelled')
                        <x-icon name="x-circle" class="w-12 h-12 text-red-500 mx-auto mb-2" />
                        <p class="text-red-600 font-semibold">DIBATALKAN</p>
                    @else
                        <x-icon name="pencil-square" class="w-12 h-12 text-yellow-500 mx-auto mb-2" />
                        <p class="text-yellow-600 font-semibold">DRAFT</p>
                    @endif
                </div>
            </x-ui.card>

            <!-- Creator Info -->
            @if($po->creator)
                <x-ui.card>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Dibuat Oleh</h3>

                    <div class="flex items-center gap-3">
                        <img src="{{ $po->creator->avatar_url }}" alt="{{ $po->creator->name }}"
                            class="w-10 h-10 rounded-full">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $po->creator->name }}</p>
                            <p class="text-xs text-gray-500">{{ $po->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </x-ui.card>
            @endif
        </div>
    </div>
</x-app-layout>