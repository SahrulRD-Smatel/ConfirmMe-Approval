<x-app-layout>
    <x-slot name="title">Riwayat Approval</x-slot>

    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-display">Riwayat Approval</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Dokumen yang telah Anda approve atau tolak
            </p>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-ui.card class="mb-6">
        <form action="{{ route('approvals.history') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                    placeholder="Cari dokumen...">
            </div>

            <div class="w-full sm:w-48">
                <select name="action"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua Aksi</option>
                    <option value="approved" {{ request('action') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('action') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <x-ui.button type="submit" variant="secondary">
                Filter
            </x-ui.button>
        </form>
    </x-ui.card>

    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <x-ui.stat-card title="Total Diproses" :value="$approvals->total()" icon="document-check" />
        <x-ui.stat-card title="Disetujui" :value="$stats['approved'] ?? 0" icon="check-circle" status="success" />
        <x-ui.stat-card title="Ditolak" :value="$stats['rejected'] ?? 0" icon="x-circle" status="danger" />
    </div>

    <!-- History Table -->
    <x-ui.card :padding="false">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            No. Dokumen
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Judul
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Keputusan
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Tanggal Aksi
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($approvals as $approval)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-primary-600 dark:text-primary-400">
                                    {{ $approval->document->document_number }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-xs">
                                    {{ $approval->document->title }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    oleh {{ $approval->document->user->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-ui.badge :status="$approval->status === 'approved' ? 'success' : 'danger'">
                                    {{ $approval->status_label }}
                                </x-ui.badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $approval->action_at?->format('d M Y H:i') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('documents.show', $approval->document) }}"
                                    class="text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                    <x-icon name="eye" class="w-5 h-5" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <x-icon name="clock" class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
                                <p class="text-gray-500 dark:text-gray-400">Belum ada riwayat approval</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($approvals->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $approvals->links() }}
            </div>
        @endif
    </x-ui.card>
</x-app-layout>