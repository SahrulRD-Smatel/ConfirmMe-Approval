<x-app-layout>
    <x-slot name="title">Detail Pengajuan Cuti</x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('leave-requests.index') }}"
                class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Detail Pengajuan Cuti</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $leaveRequest->type_label }} -
                    {{ $leaveRequest->start_date->format('d M Y') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto w-full max-w-3xl">
        <x-ui.card>
            {{-- Status Banner --}}
            <div class="mb-7 rounded-2xl border border-transparent p-5 {{ match ($leaveRequest->status) {
    'pending' => 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800',
    'approved' => 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800',
    'rejected' => 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800',
    default => 'bg-gray-50 dark:bg-gray-700'
} }}">
                <div class="flex items-center gap-3">
                    @if($leaveRequest->status === 'pending')
                        <x-icon name="clock" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                        <div>
                            <p class="font-semibold text-yellow-800 dark:text-yellow-200">Menunggu Persetujuan Atasan</p>
                            <p class="text-sm text-yellow-600 dark:text-yellow-300">Pengajuan Anda sedang direview oleh
                                atasan.</p>
                        </div>
                    @elseif($leaveRequest->status === 'pending_hr')
                        <x-icon name="clock" class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                        <div>
                            <p class="font-semibold text-orange-800 dark:text-orange-200">Menunggu Validasi HR</p>
                            <p class="text-sm text-orange-600 dark:text-orange-300">Atasan sudah menyetujui. Menunggu
                                validasi administratif dari HR.</p>
                        </div>
                    @elseif($leaveRequest->status === 'approved')
                        <x-icon name="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                        <div>
                            <p class="font-semibold text-green-800 dark:text-green-200">Disetujui</p>
                            <p class="text-sm text-green-600 dark:text-green-300">
                                Oleh {{ $leaveRequest->approver->name ?? 'Unknown' }}
                                pada {{ $leaveRequest->approved_at?->format('d M Y H:i') }}
                            </p>
                        </div>
                    @elseif($leaveRequest->status === 'rejected')
                        <x-icon name="x-circle" class="w-6 h-6 text-red-600 dark:text-red-400" />
                        <div>
                            <p class="font-semibold text-red-800 dark:text-red-200">Ditolak</p>
                            <p class="text-sm text-red-600 dark:text-red-300">
                                Oleh {{ $leaveRequest->approver->name ?? 'Unknown' }}
                            </p>
                            @if($leaveRequest->rejection_reason)
                                <p class="text-sm text-red-600 dark:text-red-300 mt-1">
                                    Alasan: {{ $leaveRequest->rejection_reason }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Leave Details --}}
            <div class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Jenis Cuti</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $leaveRequest->type_label }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Durasi</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $leaveRequest->duration }} hari</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Mulai</p>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $leaveRequest->start_date->format('l, d M Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Selesai</p>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $leaveRequest->end_date->format('l, d M Y') }}
                        </p>
                    </div>
                </div>

                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Alasan</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $leaveRequest->reason }}</p>
                </div>

                @if($leaveRequest->attachment)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Lampiran</p>
                        <a href="{{ Storage::url($leaveRequest->attachment) }}" target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            <x-icon name="document" class="w-4 h-4" />
                            Lihat Lampiran
                        </a>
                    </div>
                @endif

                <div class="border-t border-gray-200/80 pt-5 dark:border-gray-700/80">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Diajukan pada</p>
                    <p class="font-medium text-gray-900 dark:text-white">
                        {{ $leaveRequest->created_at->format('l, d M Y H:i') }}
                    </p>
                </div>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>