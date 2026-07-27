<x-app-layout>
    <x-slot name="title">Purchase Orders</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-display">Purchase Orders</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola Purchase Order</p>
            </div>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-ui.card class="mb-6">
        <form action="{{ route('finance.po.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                    placeholder="Cari nomor PO...">
            </div>
            <div class="w-full sm:w-48">
                <select name="status"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="issued" {{ request('status') === 'issued' ? 'selected' : '' }}>Issued</option>
                    <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Received</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <x-ui.button type="submit" variant="secondary">Filter</x-ui.button>
        </form>
    </x-ui.card>

    {{-- Pending Documents: Approved documents waiting for PO generation --}}
    @if($pendingDocuments->count() > 0)
        <x-ui.card class="mb-6 border-2 border-primary-200 dark:border-primary-800 bg-primary-50/50 dark:bg-primary-900/10">
            <div class="flex items-center gap-2 mb-4">
                <span class="flex h-3 w-3 relative">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-primary-500"></span>
                </span>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Menunggu Generate PO
                    <span class="text-sm font-normal text-gray-500">({{ $pendingDocuments->count() }} dokumen)</span>
                </h2>
            </div>

            <div class="space-y-3">
                @foreach($pendingDocuments as $doc)
                    <div
                        class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
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
                            <x-ui.button href="{{ route('finance.po.create', ['document_id' => $doc->id]) }}" variant="primary"
                                icon="plus" size="sm">
                                Generate PO
                            </x-ui.button>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.card>
    @endif

    <!-- PO Table -->
    <x-ui.card :padding="false">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. PO</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dokumen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($purchaseOrders as $po)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4">
                                <span
                                    class="text-sm font-medium text-primary-600 dark:text-primary-400">{{ $po->po_number }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $po->document->document_number ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $po->vendor_name }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($po->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <x-ui.badge :status="$po->status_color">{{ $po->status_label }}</x-ui.badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('finance.po.show', $po) }}"
                                        class="text-primary-600 hover:text-primary-800">
                                        <x-icon name="eye" class="w-5 h-5" />
                                    </a>
                                    <a href="{{ route('finance.po.print', $po) }}" target="_blank"
                                        class="text-green-600 hover:text-green-800">
                                        <x-icon name="printer" class="w-5 h-5" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada PO</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($purchaseOrders->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">{{ $purchaseOrders->links() }}</div>
        @endif
    </x-ui.card>
</x-app-layout>