<x-app-layout>
    <x-slot name="title">Kelola Pengumuman</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-display">Kelola Pengumuman</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage announcements for the dashboard feed.
                </p>
            </div>
            <x-ui.button href="{{ route('announcements.create') }}" icon="plus">Buat Pengumuman</x-ui.button>
        </div>
    </x-slot>

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Judul</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created By</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($announcements as $announcement)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $announcement->title }}</td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-1 rounded text-xs font-bold uppercase {{ match ($announcement->type) {
                            'urgent' => 'bg-red-100 text-red-800',
                            'warning' => 'bg-yellow-100 text-yellow-800',
                            'danger' => 'bg-red-100 text-red-800',
                            'info' => 'bg-blue-100 text-blue-800',
                            default => 'bg-gray-100 text-gray-800'
                        } }}">
                                                    {{ $announcement->type }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span
                                                    class="px-2 py-1 rounded-full text-xs {{ $announcement->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ $announcement->is_active ? 'Active' : 'Archived' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-gray-500">{{ $announcement->creator->name ?? 'System' }}</td>
                                            <td class="px-4 py-3 text-gray-500">{{ $announcement->created_at->format('d M Y') }}</td>
                                            <td class="px-4 py-3 text-right flex justify-end gap-2">
                                                <a href="{{ route('announcements.edit', $announcement) }}"
                                                    class="text-blue-600 hover:text-blue-800">
                                                    <x-icon name="pencil" class="w-5 h-5" />
                                                </a>
                                                <form action="{{ route('announcements.destroy', $announcement) }}" method="POST"
                                                    onsubmit="confirmAction(event, 'Hapus pengumuman ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                                        <x-icon name="trash" class="w-5 h-5" />
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada pengumuman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $announcements->links() }}
        </div>
    </x-ui.card>
</x-app-layout>