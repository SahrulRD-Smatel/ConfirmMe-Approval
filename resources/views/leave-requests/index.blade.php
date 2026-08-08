<x-app-layout>
    <x-slot name="title">Pengajuan Cuti Saya</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-display">Pengajuan Cuti Saya</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Riwayat dan status pengajuan cuti Anda</p>
            </div>
            <x-ui.button href="{{ route('leave-requests.create') }}" icon="plus">Ajukan Cuti</x-ui.button>
        </div>
    </x-slot>

    {{-- Leave Balance Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
        <x-ui.stat-card title="Sisa Cuti Tahunan" :value="auth()->user()->annual_leave_balance ?? 12" icon="calendar" color="primary" />
        <x-ui.stat-card title="Hari Sakit (Digunakan)" :value="auth()->user()->sick_leave_used ?? 0" icon="heart" color="danger" />
        <x-ui.stat-card title="Menunggu Persetujuan" :value="auth()->user()->leaveRequests()->pending()->count()" icon="clock" color="warning" />
    </div>

    <x-ui.card>
        @if($leaveRequests->isEmpty())
            <div class="text-center py-12">
                <div
                    class="bg-gray-50 dark:bg-gray-700 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="calendar" class="w-8 h-8 text-gray-400" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum Ada Pengajuan</h3>
                <p class="text-gray-500 text-sm mt-1">Anda belum pernah mengajukan cuti.</p>
                <div class="mt-4">
                    <x-ui.button href="{{ route('leave-requests.create') }}" icon="plus">Ajukan Cuti Pertama</x-ui.button>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Durasi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Alasan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($leaveRequests as $leave)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                                {{ $leave->start_date->format('d M Y') }}
                                                @if($leave->start_date->ne($leave->end_date))
                                                    <span class="text-gray-400">-</span> {{ $leave->end_date->format('d M Y') }}
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-1 rounded text-xs font-medium
                                                                                                                        {{ match ($leave->type) {
                                'annual' => 'bg-blue-100 text-blue-800',
                                'sick' => 'bg-red-100 text-red-800',
                                'unpaid' => 'bg-gray-100 text-gray-800',
                                default => 'bg-purple-100 text-purple-800'
                            } }}">
                                                    {{ $leave->type_label }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-gray-500">{{ $leave->duration }} hari</td>
                                            <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ $leave->reason }}</td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                                    {{ match ($leave->status) {
                                                        'pending', 'pending_supervisor', 'pending_manager' => 'bg-yellow-100 text-yellow-800',
                                                        'pending_hrd', 'pending_hr', 'pending_hr_final' => 'bg-orange-100 text-orange-800',
                                                        'approved' => 'bg-green-100 text-green-800',
                                                        'rejected' => 'bg-red-100 text-red-800',
                                                        default => 'bg-gray-100 text-gray-800'
                                                    } }}">
                                                    {{ $leave->status_label }}
                                                </span>
                                                @if(!in_array($leave->status, ['approved', 'rejected']))
                                                    <p class="text-xs text-gray-400 mt-1">{{ $leave->approver_info }}</p>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <a href="{{ route('leave-requests.show', $leave) }}"
                                                    class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $leaveRequests->links() }}
            </div>
        @endif
    </x-ui.card>
</x-app-layout>