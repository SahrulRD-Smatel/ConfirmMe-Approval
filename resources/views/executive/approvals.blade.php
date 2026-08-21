<x-app-layout>
    <x-slot name="title">Approval Queue - Executive</x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    Approval Queue
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Dokumen menunggu persetujuan Presiden Direktur
                </p>
            </div>
        </div>
    </x-slot>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft border border-gray-200/80 dark:border-gray-700/80 p-4 mb-6">
        <form method="GET" action="{{ route('executive.approvals') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tipe</label>
                <select name="type" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-xl focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Semua Tipe</option>
                    <option value="po" {{ request('type') == 'po' ? 'selected' : '' }}>Purchase Order</option>
                    <option value="invoice" {{ request('type') == 'invoice' ? 'selected' : '' }}>Invoice</option>
                    <option value="loan" {{ request('type') == 'loan' ? 'selected' : '' }}>Employee Loan</option>
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Priority</label>
                <select name="priority" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-xl focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Semua Priority</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Divisi</label>
                <select name="division" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-xl focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Semua Divisi</option>
                    <option value="IT" {{ request('division') == 'IT' ? 'selected' : '' }}>IT</option>
                    <option value="HR" {{ request('division') == 'HR' ? 'selected' : '' }}>HR</option>
                    <option value="Finance" {{ request('division') == 'Finance' ? 'selected' : '' }}>Finance</option>
                    <option value="Sales" {{ request('division') == 'Sales' ? 'selected' : '' }}>Sales</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-xl">
                    Filter
                </button>
                <a href="{{ route('executive.approvals') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Documents Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft border border-gray-200/80 dark:border-gray-700/80">
        @if($documents->isEmpty())
            <div class="px-6 py-16 text-center">
                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-lg font-medium text-gray-900 dark:text-white mb-1">Tidak ada dokumen pending</p>
                <p class="text-gray-500 dark:text-gray-400">Semua dokumen sudah diproses</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-[900px] w-full divide-y divide-gray-200/80 dark:divide-gray-700/80">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dokumen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Divisi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nominal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Menunggu</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200/80 dark:divide-gray-700/80">
                        @foreach($documents as $document)
                            <tr class="hover:bg-primary-50/60 dark:hover:bg-gray-700/50 {{ $document->priority === 'urgent' ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $document->title }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $document->document_number }}</div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">dari {{ $document->user->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $typeColors = [
                                            'po' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                            'invoice' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
                                            'loan' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                        ];
                                        $typeLabels = ['po' => 'PO', 'invoice' => 'Invoice', 'loan' => 'Loan'];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $typeColors[$document->approval_type] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $typeLabels[$document->approval_type] ?? ucfirst($document->approval_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $document->department ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $financeApprovalExec = $document->approvals()
                                            ->where('approver_role', 'finance')
                                            ->where('status', 'approved')
                                            ->whereNotNull('approved_amount')
                                            ->first();
                                        $originalAmount = ($document->approval_type === 'loan' && isset($document->metadata['loan_amount']))
                                            ? $document->metadata['loan_amount']
                                            : ($document->estimated_budget ?? 0);
                                    @endphp
                                    @if($financeApprovalExec && (float) $financeApprovalExec->approved_amount !== (float) $originalAmount)
                                        <div class="text-xs text-gray-400 line-through">
                                            Rp {{ number_format($originalAmount, 0, ',', '.') }}
                                        </div>
                                        <div class="text-sm font-semibold text-primary-600 dark:text-primary-400">
                                            Rp {{ number_format($financeApprovalExec->approved_amount, 0, ',', '.') }}
                                        </div>
                                    @else
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            Rp {{ number_format($originalAmount, 0, ',', '.') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $priorityColors = [
                                            'urgent' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                            'high' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                            'normal' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                            'low' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $priorityColors[$document->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($document->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $document->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('executive.approvals.show', $document) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-xl">
                                        Review
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200/80 dark:border-gray-700/80">
                {{ $documents->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
