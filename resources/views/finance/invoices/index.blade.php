<x-app-layout>
    <x-slot name="title">Invoice</x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Invoice</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola tagihan & pembayaran</p>
            </div>
        </div>
    </x-slot>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <x-ui.stat-card title="Total Invoice" :value="$invoices->total()" icon="document-text" />
        <x-ui.stat-card title="Belum Bayar" :value="'Rp ' . number_format($unpaidAmount, 0, ',', '.')" icon="clock"
            status="warning" />
        <x-ui.stat-card title="Lunas" :value="'Rp ' . number_format($paidAmount, 0, ',', '.')" icon="check-circle"
            status="success" />
        <x-ui.stat-card title="Overdue" :value="$overdueCount" icon="exclamation-circle" status="danger" />
    </div>

    <!-- Filters -->
    <x-ui.card class="mb-6">
        <form action="{{ route('finance.invoices.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10"
                    placeholder="Cari invoice...">
            </div>
            <div class="w-full sm:w-48">
                <select name="status"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            <x-ui.button type="submit" variant="secondary">Filter</x-ui.button>
        </form>
    </x-ui.card>

    {{-- Pending Documents: Approved documents waiting for Invoice generation --}}
    @if($pendingDocuments->count() > 0)
        <x-ui.card class="mb-6 border-2 border-orange-200 dark:border-orange-800 bg-orange-50/50 dark:bg-orange-900/10">
            <div class="flex items-center gap-2 mb-4">
                <span class="flex h-3 w-3 relative">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                </span>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Menunggu Generate Invoice
                    <span class="text-sm font-normal text-gray-500">({{ $pendingDocuments->count() }} dokumen)</span>
                </h2>
            </div>

            <div class="space-y-3">
                @foreach($pendingDocuments as $doc)
                    <div
                        class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200/80 dark:border-gray-700/80">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-medium text-primary-600 dark:text-primary-400">
                                    {{ $doc->document_number }}
                                </span>
                                <x-ui.badge status="approved">Disetujui</x-ui.badge>
                            </div>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white truncate">{{ $doc->title }}</p>
                            <div class="mt-1 flex items-center gap-4 text-xs text-gray-500">
                                <span>Oleh: {{ $doc->user->name }}</span>
                                @if($doc->approved_budget)
                                    <span class="font-medium text-green-600">Budget: Rp
                                        {{ number_format($doc->approved_budget, 0, ',', '.') }}</span>
                                @elseif($doc->estimated_budget)
                                    <span>Estimasi: Rp {{ number_format($doc->estimated_budget, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 ml-4">
                            <a href="{{ route('documents.show', $doc) }}"
                                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                <x-icon name="eye" class="w-5 h-5" />
                            </a>
                            <x-ui.button href="{{ route('finance.invoices.create', ['document_id' => $doc->id]) }}"
                                variant="primary" icon="plus" size="sm">
                                Generate Invoice
                            </x-ui.button>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.card>
    @endif

    <!-- Invoice Table -->
    <x-ui.card :padding="false">
        <div class="overflow-x-auto">
            <table class="min-w-[900px] w-full divide-y divide-gray-200/80 dark:divide-gray-700/80">
                <thead class="bg-gray-50/80 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Invoice</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jatuh Tempo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200/80 dark:divide-gray-700/80">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-primary-50/60 dark:hover:bg-gray-800/80">
                            <td class="px-6 py-4">
                                <span
                                    class="text-sm font-medium text-primary-600 dark:text-primary-400">{{ $invoice->invoice_number }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $invoice->vendor_name }}</td>
                            <td
                                class="px-6 py-4 text-sm {{ $invoice->isOverdue() ? 'text-danger-600 font-medium' : 'text-gray-700 dark:text-gray-300' }}">
                                {{ $invoice->due_date?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <x-ui.badge :status="$invoice->status_color">{{ $invoice->status_label }}</x-ui.badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <a href="{{ route('finance.invoices.show', $invoice) }}"
                                        class="text-primary-600 hover:text-primary-800">
                                        <x-icon name="eye" class="w-5 h-5" />
                                    </a>
                                    <a href="{{ route('finance.invoices.print', $invoice) }}" target="_blank"
                                        class="text-green-600 hover:text-green-800">
                                        <x-icon name="printer" class="w-5 h-5" />
                                    </a>
                                    @if($invoice->status !== 'paid')
                                        <form action="{{ route('finance.invoices.payment', $invoice) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit" class="text-success-600 hover:text-success-800"
                                                title="Tandai Lunas">
                                                <x-icon name="banknotes" class="w-5 h-5" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada invoice</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
            <div class="px-6 py-4 border-t border-gray-200/80 dark:border-gray-700/80">{{ $invoices->links() }}</div>
        @endif
    </x-ui.card>
</x-app-layout>