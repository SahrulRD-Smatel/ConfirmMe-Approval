<x-app-layout>
    <x-slot name="title">Arsip Dokumen</x-slot>

    <x-slot name="header">
        <div>
            <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Arsip Dokumen</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Riwayat semua dokumen yang telah selesai diproses
            </p>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-ui.card class="mb-6">
        <form action="{{ route('archive.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10"
                    placeholder="Cari nomor atau judul dokumen...">
            </div>

            <div class="w-full sm:w-48">
                <select name="status"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    <option value="">Semua Status</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Dibayar</option>
                </select>
            </div>

            <div class="w-full sm:w-48">
                <select name="year"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    <option value="">Semua Tahun</option>
                    @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <x-ui.button type="submit" variant="secondary">
                Filter
            </x-ui.button>
        </form>
    </x-ui.card>

    <!-- Statistics -->
    <div class="mb-7 grid grid-cols-2 gap-4 md:grid-cols-4">
        <x-ui.stat-card title="Total Arsip" :value="$documents->total()" icon="archive-box" />
        <x-ui.stat-card title="Disetujui" :value="$stats['approved'] ?? 0" icon="check-circle" status="success" />
        <x-ui.stat-card title="Ditolak" :value="$stats['rejected'] ?? 0" icon="x-circle" status="danger" />
        <x-ui.stat-card title="Selesai" :value="$stats['completed'] ?? 0" icon="document-check" status="info" />
    </div>

    <!-- Archive Table -->
    <x-ui.card :padding="false">
        <div class="overflow-x-auto">
            <table class="min-w-[720px] w-full divide-y divide-gray-200/80 dark:divide-gray-700/80">
                <thead class="bg-gray-50/80 dark:bg-gray-800">
                    <tr>
                        <th scope="col"
                            class="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            No. Dokumen
                        </th>
                        <th scope="col"
                            class="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Judul
                        </th>
                        <th scope="col"
                            class="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col"
                            class="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Tanggal Selesai
                        </th>
                        <th scope="col"
                            class="px-5 py-3.5 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200/80 dark:divide-gray-700/80">
                    @forelse($documents as $document)
                        <tr class="hover:bg-primary-50/60 dark:hover:bg-gray-800/80 transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-primary-600 dark:text-primary-400">
                                    {{ $document->document_number }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-xs">
                                    {{ $document->title }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    oleh {{ $document->user->name }}
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <x-ui.badge :status="$document->status_color">
                                    {{ $document->status_label }}
                                </x-ui.badge>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ ($document->completed_at ?? $document->approved_at ?? $document->rejected_at)?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <a href="{{ route('documents.show', $document) }}"
                                        class="text-primary-600 hover:text-primary-800 dark:text-primary-400" title="Lihat">
                                        <x-icon name="eye" class="w-5 h-5" />
                                    </a>
                                    @can('print', $document)
                                        <a href="{{ route('documents.print', $document) }}" target="_blank"
                                            class="text-green-600 hover:text-green-800 dark:text-green-400" title="Cetak"
                                            onclick="setTimeout(() => { let f = document.createElement('iframe'); f.style.display='none'; f.src='{{ route('documents.download', $document) }}'; document.body.appendChild(f); setTimeout(() => f.remove(), 10000); }, 500)">
                                            <x-icon name="printer" class="w-5 h-5" />
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <x-icon name="archive-box"
                                    class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
                                <p class="text-gray-500 dark:text-gray-400">Belum ada dokumen di arsip</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($documents->hasPages())
            <div class="px-5 py-4 border-t border-gray-200/80 dark:border-gray-700/80">
                {{ $documents->links() }}
            </div>
        @endif
    </x-ui.card>
</x-app-layout>