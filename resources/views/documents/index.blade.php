<x-app-layout>
    <x-slot name="title">Daftar Dokumen</x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Daftar Dokumen</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola semua pengajuan dokumen Anda
                </p>
            </div>

            @can('create', App\Models\Document::class)
                <x-ui.button href="{{ route('documents.create') }}" icon="plus-circle">
                    Buat Pengajuan Baru
                </x-ui.button>
            @endcan
        </div>
    </x-slot>

    <!-- Filters -->
    <x-ui.card class="mb-7">
        <form action="{{ route('documents.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="sr-only">Cari</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-icon name="eye" class="h-5 w-5 text-gray-400" />
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-primary-500/20/10 focus:border-transparent"
                        placeholder="Cari nomor atau judul dokumen...">
                </div>
            </div>

            <div class="w-full sm:w-48">
                <select name="status"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <div class="w-full sm:w-48">
                <select name="approval_type"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    <option value="">Semua Jenis</option>
                    <option value="only" {{ request('approval_type') === 'only' ? 'selected' : '' }}>Approval Only
                    </option>
                    <option value="po" {{ request('approval_type') === 'po' ? 'selected' : '' }}>Approval to PO</option>
                    <option value="invoice" {{ request('approval_type') === 'invoice' ? 'selected' : '' }}>Approval to
                        Invoice</option>
                    <option value="loan" {{ request('approval_type') === 'loan' ? 'selected' : '' }}>Employee Loan
                    </option>
                </select>
            </div>

            <x-ui.button type="submit" variant="secondary">
                Filter
            </x-ui.button>
        </form>
    </x-ui.card>

    <!-- Documents Table -->
    <x-ui.card :padding="false">
        <div class="overflow-x-auto">
            <table class="min-w-[760px] w-full divide-y divide-gray-200/80 dark:divide-gray-700/80">
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
                            Jenis
                        </th>
                        <th scope="col"
                            class="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col"
                            class="px-5 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Tanggal
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
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $document->approval_type_label }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <x-ui.badge :status="$document->status_color">
                                    {{ $document->status_label }}
                                </x-ui.badge>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $document->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <a href="{{ route('documents.show', $document) }}"
                                        class="text-primary-600 hover:text-primary-800 dark:text-primary-400" title="Lihat">
                                        <x-icon name="eye" class="w-5 h-5" />
                                    </a>

                                    @can('update', $document)
                                        <a href="{{ route('documents.edit', $document) }}"
                                            class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400" title="Edit">
                                            <x-icon name="pencil" class="w-5 h-5" />
                                        </a>
                                    @endcan

                                    @can('print', $document)
                                        <a href="{{ route('documents.print', $document) }}" target="_blank"
                                            class="text-green-600 hover:text-green-800 dark:text-green-400" title="Cetak">
                                            <x-icon name="printer" class="w-5 h-5" />
                                        </a>
                                    @endcan

                                    @can('delete', $document)
                                        <form action="{{ route('documents.destroy', $document) }}" method="POST"
                                            onsubmit="confirmAction(event, 'Apakah Anda yakin ingin menghapus dokumen ini?')"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400"
                                                title="Hapus">
                                                <x-icon name="trash" class="w-5 h-5" />
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <x-icon name="document-text"
                                    class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
                                <p class="text-gray-500 dark:text-gray-400">Belum ada dokumen</p>
                                @can('create', App\Models\Document::class)
                                    <x-ui.button href="{{ route('documents.create') }}" variant="ghost" size="sm" class="mt-4">
                                        Buat Pengajuan Pertama
                                    </x-ui.button>
                                @endcan
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