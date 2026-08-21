<x-app-layout>
    <x-slot name="title">Laporan Dokumen</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-down">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Laporan Dokumen</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Detail semua dokumen berdasarkan filter
                </p>
            </div>
            <x-ui.button href="{{ route('reports.index') }}" variant="secondary" icon="arrow-left" size="sm">
                Kembali
            </x-ui.button>
        </div>
    </x-slot>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200/80 dark:border-gray-700/80 shadow-soft mb-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select name="status"
                    class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-primary-500/20 focus:border-primary-500">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_review" {{ request('status') === 'in_review' ? 'selected' : '' }}>In Review</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tipe Approval</label>
                <select name="approval_type"
                    class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-primary-500/20 focus:border-primary-500">
                    <option value="">Semua Tipe</option>
                    <option value="only" {{ request('approval_type') === 'only' ? 'selected' : '' }}>Approval Only</option>
                    <option value="po" {{ request('approval_type') === 'po' ? 'selected' : '' }}>Purchase Order</option>
                    <option value="invoice" {{ request('approval_type') === 'invoice' ? 'selected' : '' }}>Invoice</option>
                    <option value="loan" {{ request('approval_type') === 'loan' ? 'selected' : '' }}>Employee Loan</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Dari Tanggal</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}"
                    class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-primary-500/20 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sampai Tanggal</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}"
                    class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-primary-500/20 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Departemen</label>
                <input type="text" name="department" value="{{ request('department') }}" placeholder="Semua"
                    class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-primary-500/20 focus:border-primary-500">
            </div>
            <div class="sm:col-span-2 lg:col-span-5 flex items-center gap-2">
                <x-ui.button type="submit" icon="funnel" size="sm">Filter</x-ui.button>
                <x-ui.button type="button" variant="ghost" size="sm"
                    onclick="window.location='{{ route('reports.documents') }}'">Reset</x-ui.button>
                <span class="ml-auto text-sm text-gray-500">
                    {{ $documents->total() }} dokumen ditemukan
                </span>
            </div>
        </form>
    </div>

    {{-- Documents Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-[1000px] w-full divide-y divide-gray-200/80 dark:divide-gray-700/80">
                <thead class="bg-gray-50/80 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No. Dokumen</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Judul</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pemohon</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Departemen</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipe</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nominal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($documents as $document)
                        <tr class="hover:bg-primary-50/60 dark:hover:bg-gray-700/50 transition">
                            <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400">
                                {{ $document->document_number }}
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('documents.show', $document) }}"
                                    class="font-medium text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                    {{ Str::limit($document->title, 40) }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                {{ $document->user->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                {{ $document->department ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $typeColors = [
                                        'only' => 'bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-200',
                                        'po' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                                        'invoice' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
                                        'loan' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                                    ];
                                    $typeLabels = [
                                        'only' => 'Approval',
                                        'po' => 'PO',
                                        'invoice' => 'Invoice',
                                        'loan' => 'Loan',
                                    ];
                                @endphp
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$document->approval_type] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $typeLabels[$document->approval_type] ?? ucfirst($document->approval_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <x-ui.badge :status="$document->status_color">
                                    {{ $document->status_label }}
                                </x-ui.badge>
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">
                                @if($document->estimated_budget)
                                    Rp {{ number_format($document->estimated_budget, 0, ',', '.') }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">
                                {{ $document->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <x-icon name="document-text" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                                <p class="text-gray-500 dark:text-gray-400">Tidak ada dokumen ditemukan</p>
                                <p class="text-sm text-gray-400 mt-1">Coba ubah filter pencarian Anda</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($documents->hasPages())
            <div class="px-4 py-3 border-t border-gray-200/80 dark:border-gray-700/80">
                {{ $documents->links() }}
            </div>
        @endif
    </div>

    <style>
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down {
            animation: fadeInDown 0.5s ease-out;
        }
    </style>
</x-app-layout>
