<x-app-layout>
    <x-slot name="title">Approval Cuti</x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Approval Cuti</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola pengajuan cuti dari tim Anda
                </p>
            </div>
        </div>
    </x-slot>

    {{-- Pending Requests --}}
    @if($pendingRequests->count() > 0)
        <div class="mb-8 space-y-7" x-data="{ 
            activeLeave: null,
            showConfirmModal: false,
            action: null,
            rejectReason: '',
            approveComment: '',
            openReview(id) { this.activeLeave = id; this.action = null; this.rejectReason = ''; this.approveComment = ''; },
            closeReview() { this.activeLeave = null; this.action = null; },
         }">
            {{-- Table View --}}
            <x-ui.card class="mb-6 p-0 overflow-hidden border-0 shadow-soft ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-[900px] w-full divide-y divide-gray-200/80 dark:divide-gray-700/80">
                        <thead class="bg-gray-50/80 dark:bg-gray-800/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Karyawan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis Cuti</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Durasi</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200/80 dark:divide-gray-700/80">
                            @foreach($pendingRequests as $leave)
                                <tr class="hover:bg-primary-50/60 dark:hover:bg-gray-800/80/50 transition-colors" :class="{'bg-primary-50 dark:bg-primary-900/20': activeLeave === {{ $leave->id }}}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $leave->user->avatar_url }}" alt="{{ $leave->user->name }}" 
                                                class="w-10 h-10 rounded-full object-cover border-2 border-white dark:border-gray-700 shadow-soft">
                                            <div>
                                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ $leave->user->name }}</h3>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $leave->user->position ?? 'Staff' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                                        {{ $leave->type_label }}
                                        @if($leave->attachment)
                                            <x-icon name="paper-clip" class="w-4 h-4 text-blue-500 inline-block ml-1" title="Ada Lampiran" />
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                                        {{ $leave->duration }} Hari
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-ui.badge :status="$leave->status_color" size="sm">
                                            {{ $leave->status_label }}
                                        </x-ui.badge>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button type="button" @click="openReview({{ $leave->id }})" 
                                            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-4 focus:ring-primary-500/20/10 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <x-icon name="eye" class="w-4 h-4 mr-2" />
                                            Review
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>

            {{-- Detail & Approval Panel (shows below cards, matching approvals/show.blade.php pattern) --}}
            @foreach($pendingRequests as $leave)
                <div x-show="activeLeave === {{ $leave->id }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
                        {{-- Main Content --}}
                        <div class="xl:col-span-2 space-y-6">
                            {{-- Leave Detail Card --}}
                            <x-ui.card>
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Detail Pengajuan Cuti</h2>
                                    <button @click="closeReview()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                        <x-icon name="x-mark" class="w-5 h-5" />
                                    </button>
                                </div>

                                {{-- Employee Info --}}
                                <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl mb-6">
                                    <img src="{{ $leave->user->avatar_url }}" alt="{{ $leave->user->name }}"
                                        class="w-14 h-14 rounded-full object-cover border-2 border-white dark:border-gray-600 shadow-soft">
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ $leave->user->name }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $leave->user->department }} — {{ $leave->user->position ?? 'Staff' }}</p>
                                    </div>
                                </div>

                                {{-- Leave Details Grid --}}
                                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jenis Cuti</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $leave->type_label }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Saldo Cuti Tersisa</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $leave->user->annual_leave_balance }} Hari</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Mulai</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $leave->start_date->format('d F Y') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Selesai</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $leave->end_date->format('d F Y') }} ({{ $leave->duration }} Hari)</dd>
                                    </div>
                                </dl>

                                {{-- Reason --}}
                                <div class="border-t border-gray-200/80 dark:border-gray-700/80 pt-4">
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Alasan</h3>
                                    <div class="prose prose-sm dark:prose-invert max-w-none">
                                        {!! nl2br(e($leave->reason)) !!}
                                    </div>
                                </div>
                            </x-ui.card>

                            {{-- Attachment --}}
                            @if($leave->attachment)
                                <x-ui.card>
                                    <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Lampiran</h2>
                                    <a href="{{ Storage::url($leave->attachment) }}" target="_blank"
                                        class="flex items-center gap-3 p-3 bg-gray-50/80 dark:bg-gray-800 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                            <x-icon name="document-text" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">Lihat Lampiran</p>
                                            <p class="text-xs text-gray-500">Klik untuk membuka file</p>
                                        </div>
                                        <x-icon name="arrow-top-right-on-square" class="w-4 h-4 text-gray-400" />
                                    </a>
                                </x-ui.card>
                            @endif
                        </div>

                        {{-- Approval Action Sidebar (matching approvals/show.blade.php) --}}
                        <div class="space-y-6">
                            <x-ui.card>
                                <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Keputusan Anda</h2>

                                <div class="space-y-4">
                                    {{-- Action Buttons --}}
                                    <div class="grid grid-cols-2 gap-2">
                                        <button type="button" @click="action = 'approve'"
                                            :class="action === 'approve' ? 'ring-2 ring-success-500 bg-success-50 dark:bg-success-900/30' : 'bg-gray-50 dark:bg-gray-800'"
                                            class="flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200/80 dark:border-gray-700/80 hover:border-success-300 transition-all">
                                            <x-icon name="check-circle" class="w-8 h-8 text-success-600 mb-2" />
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Setujui</span>
                                        </button>

                                        <button type="button" @click="action = 'reject'"
                                            :class="action === 'reject' ? 'ring-2 ring-danger-500 bg-danger-50 dark:bg-danger-900/30' : 'bg-gray-50 dark:bg-gray-800'"
                                            class="flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200/80 dark:border-gray-700/80 hover:border-danger-300 transition-all">
                                            <x-icon name="x-circle" class="w-8 h-8 text-danger-600 mb-2" />
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Tolak</span>
                                        </button>
                                    </div>

                                    {{-- Approve Form --}}
                                    <div x-show="action === 'approve'" x-transition class="space-y-4">
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">
                                                Catatan (Opsional)
                                            </label>
                                            <textarea x-model="approveComment" rows="3"
                                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10"
                                                placeholder="Tambahkan catatan..."></textarea>
                                        </div>

                                        <button type="button" @click="showConfirmModal = true"
                                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-success-600 hover:bg-success-700 text-white font-medium rounded-xl transition-colors">
                                            <x-icon name="check-circle" class="w-5 h-5" />
                                            Lanjutkan Persetujuan
                                        </button>
                                    </div>

                                    {{-- Reject Form --}}
                                    <div x-show="action === 'reject'" x-transition class="space-y-4">
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">
                                                Alasan Penolakan <span class="text-danger-500">*</span>
                                            </label>
                                            <textarea x-model="rejectReason" rows="3"
                                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10"
                                                placeholder="Jelaskan alasan penolakan..."></textarea>
                                        </div>

                                        <button type="button" @click="rejectReason.trim() ? showConfirmModal = true : showAlert('Alasan penolakan wajib diisi')"
                                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-danger-600 hover:bg-danger-700 text-white font-medium rounded-xl transition-colors">
                                            <x-icon name="x-circle" class="w-5 h-5" />
                                            Lanjutkan Penolakan
                                        </button>
                                    </div>
                                </div>
                            </x-ui.card>
                        </div>
                    </div>

                    {{-- Confirmation Modal (matching approvals/show.blade.php pattern) --}}
                    <div x-show="showConfirmModal" x-transition
                        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/70 p-4 backdrop-blur-sm"
                        @click.self="showConfirmModal = false"
                        style="display: none;">
                        <div class="w-full max-w-md rounded-3xl border border-gray-200/80 bg-white p-6 shadow-soft-lg dark:border-gray-700 dark:bg-gray-900" @click.stop>
                            {{-- Approve Confirmation --}}
                            <template x-if="action === 'approve'">
                                <div>
                                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-success-100 dark:bg-success-900/30 rounded-full">
                                        <x-icon name="check-circle" class="w-10 h-10 text-success-600" />
                                    </div>
                                    <h3 class="text-xl font-semibold text-center text-gray-900 dark:text-white mb-2">
                                        Konfirmasi Persetujuan
                                    </h3>
                                    <p class="text-center text-gray-600 dark:text-gray-400 mb-6">
                                        Anda yakin ingin <strong class="text-success-600">MENYETUJUI</strong> pengajuan cuti <strong>{{ $leave->user->name }}</strong>?
                                        <br><span class="text-sm">Tindakan ini tidak dapat dibatalkan.</span>
                                    </p>

                                    <form action="{{ route('leave-approvals.approve', $leave) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="comment" :value="approveComment">

                                        <div class="flex gap-3">
                                            <button type="button" @click="showConfirmModal = false"
                                                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-primary-50/60 dark:hover:bg-gray-700 transition-colors">
                                                Batal
                                            </button>
                                            <button type="submit"
                                                class="flex-1 px-4 py-2 bg-success-600 hover:bg-success-700 text-white rounded-xl transition-colors">
                                                Ya, Setujui
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </template>

                            {{-- Reject Confirmation --}}
                            <template x-if="action === 'reject'">
                                <div>
                                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-danger-100 dark:bg-danger-900/30 rounded-full">
                                        <x-icon name="x-circle" class="w-10 h-10 text-danger-600" />
                                    </div>
                                    <h3 class="text-xl font-semibold text-center text-gray-900 dark:text-white mb-2">
                                        Konfirmasi Penolakan
                                    </h3>
                                    <p class="text-center text-gray-600 dark:text-gray-400 mb-6">
                                        Anda yakin ingin <strong class="text-danger-600">MENOLAK</strong> pengajuan cuti <strong>{{ $leave->user->name }}</strong>?
                                        <br><span class="text-sm">Tindakan ini tidak dapat dibatalkan.</span>
                                    </p>

                                    <form action="{{ route('leave-approvals.reject', $leave) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="rejection_reason" :value="rejectReason">

                                        <div class="flex gap-3">
                                            <button type="button" @click="showConfirmModal = false"
                                                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-primary-50/60 dark:hover:bg-gray-700 transition-colors">
                                                Batal
                                            </button>
                                            <button type="submit"
                                                class="flex-1 px-4 py-2 bg-danger-600 hover:bg-danger-700 text-white rounded-xl transition-colors">
                                                Ya, Tolak
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="mt-6">
                {{ $pendingRequests->links() }}
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <x-ui.card class="text-center py-12 mb-6">
            <x-icon name="check-circle" class="w-16 h-16 text-success-500 mx-auto mb-4" />
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Semua Beres! 🎉</h3>
            <p class="text-gray-500 dark:text-gray-400">Tidak ada pengajuan cuti yang menunggu persetujuan Anda.</p>
        </x-ui.card>
    @endif

    {{-- Recently Processed --}}
    @if($recentlyProcessed->isNotEmpty())
        <div class="mt-8">
            <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Diproses Minggu Ini</h2>
            <x-ui.card>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 font-medium">
                            <tr>
                                <th class="px-4 py-3">Karyawan</th>
                                <th class="px-4 py-3">Jenis</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Diproses</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200/80 dark:divide-gray-700/80">
                            @foreach($recentlyProcessed as $leave)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <img src="{{ $leave->user->avatar_url }}" alt="{{ $leave->user->name }}"
                                                class="w-6 h-6 rounded-full">
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $leave->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ $leave->type_label }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $leave->start_date->format('d M') }} ({{ $leave->duration }} hari)</td>
                                    <td class="px-4 py-3">
                                        <x-ui.badge :color="$leave->status_color">
                                            {{ $leave->status_label }}
                                        </x-ui.badge>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ $leave->approved_at?->diffForHumans() ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>
    @endif
</x-app-layout>