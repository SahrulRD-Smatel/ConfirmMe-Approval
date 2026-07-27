<x-app-layout>
    <x-slot name="title">{{ $invoice->invoice_number }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('finance.invoices.index') }}"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                </a>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-display">
                            {{ $invoice->invoice_number }}
                        </h1>
                        <x-ui.badge :status="$invoice->status === 'paid' ? 'success' : ($invoice->status === 'overdue' ? 'danger' : 'warning')">
                            {{ ucfirst($invoice->status) }}
                        </x-ui.badge>
                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $invoice->vendor_name }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if($invoice->status !== 'paid')
                    <form action="{{ route('finance.invoices.payment', $invoice) }}" method="POST" class="inline">
                        @csrf
                        <x-ui.button type="submit" variant="success" icon="banknotes" size="sm">
                            Tandai Lunas
                        </x-ui.button>
                    </form>
                @endif
                <x-ui.button href="{{ route('finance.invoices.print', $invoice) }}" target="_blank" variant="secondary"
                    icon="printer" size="sm">
                    Cetak
                </x-ui.button>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Invoice Info -->
            <x-ui.card>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Invoice</h2>

                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Nomor Invoice</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $invoice->invoice_number }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Vendor</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $invoice->vendor_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Tanggal Invoice</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $invoice->invoice_date->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Jatuh Tempo</dt>
                        <dd
                            class="text-sm font-medium {{ $invoice->due_date < now() && $invoice->status !== 'paid' ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                            {{ $invoice->due_date->format('d M Y') }}
                        </dd>
                    </div>
                </dl>
            </x-ui.card>

            <!-- Document Reference -->
            @if($invoice->document)
                <x-ui.card>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Dokumen Referensi</h2>

                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $invoice->document->document_number }}</p>
                            <p class="text-sm text-gray-500">{{ $invoice->document->title }}</p>
                        </div>
                        <a href="{{ route('documents.show', $invoice->document) }}"
                            class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                            Lihat Dokumen →
                        </a>
                    </div>
                </x-ui.card>
            @endif

            <!-- Notes -->
            @if($invoice->notes)
                <x-ui.card>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Catatan</h2>
                    <p class="text-gray-700 dark:text-gray-300">{{ $invoice->notes }}</p>
                </x-ui.card>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Amount Summary -->
            <x-ui.card>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Ringkasan Pembayaran</h3>

                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="text-gray-900 dark:text-white">Rp
                            {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Pajak</span>
                        <span class="text-gray-900 dark:text-white">Rp
                            {{ number_format($invoice->tax_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t pt-3 flex justify-between">
                        <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                        <span class="font-bold text-lg text-primary-600">Rp
                            {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($invoice->paid_amount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Terbayar</span>
                            <span class="text-green-600">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Sisa</span>
                            <span class="text-red-600">Rp
                                {{ number_format($invoice->total_amount - $invoice->paid_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
            </x-ui.card>

            <!-- Status -->
            <x-ui.card>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Status</h3>

                <div class="text-center py-4">
                    @if($invoice->status === 'paid')
                        <x-icon name="check-circle" class="w-12 h-12 text-green-500 mx-auto mb-2" />
                        <p class="text-green-600 font-semibold">LUNAS</p>
                        @if($invoice->paid_at)
                            <p class="text-sm text-gray-500">{{ $invoice->paid_at->format('d M Y') }}</p>
                        @endif
                    @elseif($invoice->due_date < now())
                        <x-icon name="exclamation-circle" class="w-12 h-12 text-red-500 mx-auto mb-2" />
                        <p class="text-red-600 font-semibold">JATUH TEMPO</p>
                    @else
                        <x-icon name="clock" class="w-12 h-12 text-yellow-500 mx-auto mb-2" />
                        <p class="text-yellow-600 font-semibold">MENUNGGU PEMBAYARAN</p>
                    @endif
                </div>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>