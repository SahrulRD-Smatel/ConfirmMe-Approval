<x-app-layout>
    <x-slot name="title">{{ __('Approval Cuti') }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Approval Cuti') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Kelola pengajuan cuti dari tim Anda dengan review yang terarah.') }}</p>
            </div>
        </div>
    </x-slot>

    {{-- Pending Requests --}}
    @if($pendingRequests->count() > 0)
        <div class="space-y-5" x-data="{ 
            activeLeave: null,
            showConfirmModal: false,
            action: null,
            rejectReason: '',
            approveComment: '',
            openReview(id) { this.activeLeave = id; this.action = null; this.rejectReason = ''; this.approveComment = ''; },
            closeReview() { this.activeLeave = null; this.action = null; },
         }">
            <div class="grid grid-cols-1 items-start gap-4 xl:grid-cols-[minmax(0,1fr)_17rem]">
                <div class="min-w-0">
                    <div class="mb-3 flex items-end justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-400">Needs attention</p>
                            <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Pengajuan Menunggu Review') }}</h2>
                        </div>
                        <span class="rounded-full bg-primary-50 px-3 py-1.5 text-xs font-bold text-primary-700 dark:bg-primary-900/30 dark:text-primary-300">{{ $pendingRequests->count() }} {{ __('pengajuan') }}</span>
                    </div>

                    {{-- Table View --}}
                    <x-ui.card class="overflow-hidden rounded-2xl border-gray-200/80 p-0 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80">
                        <div class="max-h-[29rem] overflow-auto">
                            <table class="min-w-[850px] w-full divide-y divide-gray-200/80 text-sm dark:divide-gray-700/80">
                                <thead class="sticky top-0 z-10 bg-gray-50/95 shadow-sm dark:bg-gray-800/95">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-[0.12em] text-gray-500 dark:text-gray-400">{{ __('Karyawan') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-[0.12em] text-gray-500 dark:text-gray-400">{{ __('Jenis Cuti') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-[0.12em] text-gray-500 dark:text-gray-400">{{ __('Tanggal') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-[0.12em] text-gray-500 dark:text-gray-400">{{ __('Durasi') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-[0.12em] text-gray-500 dark:text-gray-400">{{ __('Status') }}</th>
                                        <th scope="col" class="px-4 py-3 text-right text-[10px] font-bold uppercase tracking-[0.12em] text-gray-500 dark:text-gray-400">{{ __('Aksi') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200/80 bg-white dark:divide-gray-700/80 dark:bg-gray-900">
                                    @foreach($pendingRequests as $leave)
                                        <tr class="transition-colors hover:bg-primary-50/60 dark:hover:bg-gray-800/80/50" :class="{'bg-primary-50 dark:bg-primary-900/20': activeLeave === {{ $leave->id }}}">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="flex items-center gap-2.5">
                                                    <img src="{{ $leave->user->avatar_url }}" alt="{{ $leave->user->name }}" class="h-9 w-9 rounded-full border-2 border-white object-cover shadow-soft dark:border-gray-700">
                                                    <div class="min-w-0">
                                                        <h3 class="max-w-[11rem] truncate text-xs font-bold text-gray-900 dark:text-white">{{ $leave->user->name }}</h3>
                                                        <p class="max-w-[11rem] truncate text-[10px] text-gray-500 dark:text-gray-400">{{ $leave->user->position ?? 'Staff' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-900 dark:text-white">
                                                {{ $leave->type_label }}
                                                @if($leave->attachment)
                                                    <x-icon name="paper-clip" class="ml-1 inline-block h-3.5 w-3.5 text-blue-500" title="Ada Lampiran" />
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">{{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M Y') }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-900 dark:text-white">{{ $leave->duration }} {{ __('Hari') }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <x-ui.badge :status="$leave->status_color" size="sm">
                                                    {{ $leave->status_label }}
                                                </x-ui.badge>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                <button type="button" @click="openReview({{ $leave->id }})" class="inline-flex h-8 items-center rounded-lg bg-primary-600 px-3 text-[10px] font-bold uppercase tracking-wider text-white transition hover:bg-primary-700 focus:outline-none focus:ring-4 focus:ring-primary-500/20">
                                                    <x-icon name="eye" class="mr-1.5 h-3.5 w-3.5" />
                                                    Review
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="border-t border-gray-200/80 bg-gray-50/70 px-4 py-3 text-xs text-gray-500 dark:border-gray-800/80 dark:bg-gray-800/40 dark:text-gray-400">{{ __('Pilih Review untuk membuka detail dan keputusan pengajuan.') }}</div>
                    </x-ui.card>
                </div>

                <aside class="hidden space-y-3 xl:block xl:sticky xl:top-6">
                    <div class="rounded-2xl border border-primary-100 bg-primary-50/70 p-4 dark:border-primary-900/40 dark:bg-primary-900/10">
                        <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-primary-600 dark:text-primary-400">Review flow</p>
                        <h3 class="mt-1 text-sm font-bold text-primary-900 dark:text-primary-200">Satu pengajuan tiap kali</h3>
                        <p class="mt-2 text-xs leading-5 text-primary-800/75 dark:text-primary-300/75">Pilih pengajuan dari queue, cek detail, lalu tentukan setujui atau tolak.</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200/80 bg-white p-4 shadow-soft dark:border-gray-800/80 dark:bg-gray-900">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-success-500"></span>
                            <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ __('Keputusan aman') }}</p>
                        </div>
                        <p class="mt-2 text-[11px] leading-5 text-gray-500 dark:text-gray-400">{{ __('Konfirmasi tambahan akan muncul sebelum keputusan dikirim.') }}</p>
                    </div>
                </aside>
            </div>

            {{-- Detail & Approval Panel (shows below cards, matching approvals/show.blade.php pattern) --}}
            @foreach($pendingRequests as $leave)
                <div x-show="activeLeave === {{ $leave->id }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                    <div class="grid grid-cols-1 items-start gap-4 xl:grid-cols-[minmax(0,1fr)_19rem]">
                        {{-- Main Content --}}
                        <div class="min-w-0 space-y-4">
                            {{-- Leave Detail Card --}}
                            <x-ui.card class="rounded-2xl border-gray-200/80 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80">
                                <div class="mb-4 flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-primary-600 dark:text-primary-400">Request detail</p>
                                        <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Detail Pengajuan Cuti') }}</h2>
                                    </div>
                                    <button @click="closeReview()" class="rounded-full p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-300">
                                        <x-icon name="x-mark" class="h-5 w-5" />
                                    </button>
                                </div>

                                {{-- Employee Info --}}
                                <div class="mb-5 flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50/80 p-3 dark:border-gray-800 dark:bg-gray-800/60">
                                    <img src="{{ $leave->user->avatar_url }}" alt="{{ $leave->user->name }}" class="h-12 w-12 rounded-full border-2 border-white object-cover shadow-soft dark:border-gray-600">
                                    <div class="min-w-0">
                                        <h3 class="truncate text-sm font-bold text-gray-900 dark:text-white">{{ $leave->user->name }}</h3>
                                        <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $leave->user->department }} — {{ $leave->user->position ?? 'Staff' }}</p>
                                    </div>
                                </div>

                                {{-- Leave Details Grid --}}
                                <dl class="grid grid-cols-2 gap-x-4 gap-y-4 border-b border-gray-200/80 pb-4 dark:border-gray-700/80 sm:grid-cols-4">
                                    <div>
                                        <dt class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">{{ __('Jenis Cuti') }}</dt>
                                        <dd class="mt-1 text-xs font-semibold text-gray-900 dark:text-white">{{ $leave->type_label }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">{{ __('Saldo Cuti Tersisa') }}</dt>
                                        <dd class="mt-1 text-xs font-semibold text-gray-900 dark:text-white">{{ $leave->user->annual_leave_balance }} {{ __('Hari') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">{{ __('Tanggal Mulai') }}</dt>
                                        <dd class="mt-1 text-xs font-semibold text-gray-900 dark:text-white">{{ $leave->start_date->format('d F Y') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">{{ __('Tanggal Selesai') }}</dt>
                                        <dd class="mt-1 text-xs font-semibold text-gray-900 dark:text-white">{{ $leave->end_date->format('d F Y') }} ({{ $leave->duration }} {{ __('Hari') }})</dd>
                                    </div>
                                </dl>

                                {{-- Reason --}}
                                <div class="pt-4">
                                    <h3 class="mb-2 text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">{{ __('Alasan') }}</h3>
                                    <div class="prose prose-sm max-w-none leading-6 dark:prose-invert">
                                        {!! nl2br(e($leave->reason)) !!}
                                    </div>
                                </div>
                            </x-ui.card>

                            {{-- Attachment --}}
                            @if($leave->attachment)
                                <x-ui.card class="rounded-2xl border-gray-200/80 shadow-soft dark:border-gray-800/80">
                                    <div class="mb-3 flex items-center justify-between">
                                        <h2 class="text-sm font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Lampiran') }}</h2>
                                        <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">{{ __('File pendukung') }}</span>
                                    </div>
                                    <a href="{{ Storage::url($leave->attachment) }}" target="_blank" class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50/80 p-3 transition-colors hover:border-primary-100 hover:bg-primary-50 dark:border-gray-800 dark:bg-gray-800 dark:hover:bg-gray-700">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/30">
                                            <x-icon name="document-text" class="h-4.5 w-4.5 text-primary-600 dark:text-primary-400" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ __('Lihat Lampiran') }}</p>
                                            <p class="mt-0.5 text-[10px] text-gray-500">{{ __('Klik untuk membuka file') }}</p>
                                        </div>
                                        <x-icon name="arrow-top-right-on-square" class="h-4 w-4 text-gray-400" />
                                    </a>
                                </x-ui.card>
                            @endif
                        </div>

                        {{-- Approval Action Sidebar (matching approvals/show.blade.php) --}}
                        <div class="xl:sticky xl:top-6">
                            <x-ui.card class="rounded-2xl border-gray-200/80 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80">
                                <div class="mb-4">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-primary-600 dark:text-primary-400">Decision</p>
                                    <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Keputusan Anda') }}</h2>
                                </div>

                                <div class="space-y-4">
                                    {{-- Action Buttons --}}
                                    <div class="grid grid-cols-2 gap-2">
                                        <button type="button" @click="action = 'approve'"
                                            :class="action === 'approve' ? 'ring-2 ring-success-500 bg-success-50 dark:bg-success-900/30' : 'bg-gray-50 dark:bg-gray-800'"
                                            class="flex flex-col items-center justify-center rounded-xl border border-gray-200/80 p-3 transition-all hover:border-success-300 dark:border-gray-700">
                                            <x-icon name="check-circle" class="mb-1.5 h-7 w-7 text-success-600" />
                                            <span class="text-xs font-semibold text-gray-900 dark:text-white">{{ __('Setujui') }}</span>
                                        </button>

                                        <button type="button" @click="action = 'reject'"
                                            :class="action === 'reject' ? 'ring-2 ring-danger-500 bg-danger-50 dark:bg-danger-900/30' : 'bg-gray-50 dark:bg-gray-800'"
                                            class="flex flex-col items-center justify-center rounded-xl border border-gray-200/80 p-3 transition-all hover:border-danger-300 dark:border-gray-700">
                                            <x-icon name="x-circle" class="mb-1.5 h-7 w-7 text-danger-600" />
                                            <span class="text-xs font-semibold text-gray-900 dark:text-white">{{ __('Tolak') }}</span>
                                        </button>
                                    </div>

                                    {{-- Approve Form --}}
                                    <div x-show="action === 'approve'" x-transition class="space-y-3">
                                        <div>
                                            <label class="mb-2 block text-xs font-semibold text-gray-700 dark:text-gray-200">{{ __('Catatan (Opsional)') }}</label>
                                            <textarea x-model="approveComment" rows="3" class="block w-full rounded-xl border-gray-300 text-sm leading-5 shadow-sm focus:border-primary-500 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="{{ __('Tambahkan catatan...') }}"></textarea>
                                        </div>

                                        <button type="button" @click="showConfirmModal = true" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-success-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-success-700">
                                            <x-icon name="check-circle" class="h-4 w-4" />
                                            {{ __('Lanjutkan Persetujuan') }}
                                        </button>
                                    </div>

                                    {{-- Reject Form --}}
                                    <div x-show="action === 'reject'" x-transition class="space-y-3">
                                        <div>
                                            <label class="mb-2 block text-xs font-semibold text-gray-700 dark:text-gray-200">{{ __('Alasan Penolakan') }} <span class="text-danger-500">*</span></label>
                                            <textarea x-model="rejectReason" rows="3" class="block w-full rounded-xl border-gray-300 text-sm leading-5 shadow-sm focus:border-primary-500 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="{{ __('Jelaskan alasan penolakan...') }}"></textarea>
                                        </div>

                                        <button type="button" @click="rejectReason.trim() ? showConfirmModal = true : showAlert('Alasan penolakan wajib diisi')" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-danger-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-danger-700">
                                            <x-icon name="x-circle" class="h-4 w-4" />
                                            {{ __('Lanjutkan Penolakan') }}
                                        </button>
                                    </div>
                                </div>
                            </x-ui.card>
                        </div>
                    </div>

                    {{-- Confirmation Modal (matching approvals/show.blade.php pattern) --}}
                    <div x-show="showConfirmModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/70 p-4 backdrop-blur-sm" @click.self="showConfirmModal = false" style="display: none;">
                        <div class="w-full max-w-md rounded-2xl border border-gray-200/80 bg-white p-6 shadow-soft-lg dark:border-gray-700 dark:bg-gray-900" @click.stop>
                            {{-- Approve Confirmation --}}
                            <template x-if="action === 'approve'">
                                <div>
                                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-success-100 dark:bg-success-900/30">
                                        <x-icon name="check-circle" class="h-8 w-8 text-success-600" />
                                    </div>
                                    <h3 class="mb-2 text-center text-lg font-semibold text-gray-900 dark:text-white">{{ __('Konfirmasi Approval') }}</h3>
                                    <p class="mb-5 text-center text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Anda yakin ingin') }} <strong class="text-success-600">{{ __('MENYETUJUI') }}</strong> {{ __('pengajuan cuti') }} <strong>{{ $leave->user->name }}</strong>?
                                        <br><span class="text-xs">{{ __('Tindakan ini tidak dapat dibatalkan.') }}</span>
                                    </p>

                                    <form action="{{ route('leave-approvals.approve', $leave) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="comment" :value="approveComment">

                                        <div class="flex gap-2">
                                            <button type="button" @click="showConfirmModal = false" class="flex-1 rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 transition-colors hover:bg-primary-50/60 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">{{ __('Batal') }}</button>
                                            <button type="submit" class="flex-1 rounded-xl bg-success-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-success-700">{{ __('Ya, Setujui') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </template>

                            {{-- Reject Confirmation --}}
                            <template x-if="action === 'reject'">
                                <div>
                                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-danger-100 dark:bg-danger-900/30">
                                        <x-icon name="x-circle" class="h-8 w-8 text-danger-600" />
                                    </div>
                                    <h3 class="mb-2 text-center text-lg font-semibold text-gray-900 dark:text-white">{{ __('Konfirmasi Penolakan') }}</h3>
                                    <p class="mb-5 text-center text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Anda yakin ingin') }} <strong class="text-danger-600">{{ __('MENOLAK') }}</strong> {{ __('pengajuan cuti') }} <strong>{{ $leave->user->name }}</strong>?
                                        <br><span class="text-xs">{{ __('Tindakan ini tidak dapat dibatalkan.') }}</span>
                                    </p>

                                    <form action="{{ route('leave-approvals.reject', $leave) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="rejection_reason" :value="rejectReason">

                                        <div class="flex gap-2">
                                            <button type="button" @click="showConfirmModal = false" class="flex-1 rounded-xl border border-gray-300 px-4 py-2 text-sm text-gray-700 transition-colors hover:bg-primary-50/60 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">{{ __('Batal') }}</button>
                                            <button type="submit" class="flex-1 rounded-xl bg-danger-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-danger-700">{{ __('Ya, Tolak') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="border-t border-gray-200/80 pt-1 dark:border-gray-800/80">
                {{ $pendingRequests->links() }}
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <x-ui.card class="mb-6 rounded-2xl border-dashed py-14 text-center shadow-soft dark:border-gray-700">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-success-50 dark:bg-success-900/20">
                <x-icon name="check-circle" class="h-8 w-8 text-success-500" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Semua Beres!') }}</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Tidak ada pengajuan cuti yang menunggu persetujuan Anda.') }}</p>
        </x-ui.card>
    @endif

    {{-- Recently Processed --}}
    @if($recentlyProcessed->isNotEmpty())
        <div class="mt-7">
            <div class="mb-3 flex items-end justify-between gap-3">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-400">History</p>
                    <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Diproses Minggu Ini') }}</h2>
                </div>
                <span class="text-xs text-gray-400">{{ __('Riwayat keputusan terbaru') }}</span>
            </div>
            <x-ui.card class="overflow-hidden rounded-2xl border-gray-200/80 p-0 shadow-soft dark:border-gray-800/80">
                <div class="max-h-72 overflow-auto">
                    <table class="min-w-[760px] w-full text-left text-sm">
                        <thead class="sticky top-0 z-10 bg-gray-50/95 text-[10px] font-bold uppercase tracking-[0.12em] text-gray-500 shadow-sm dark:bg-gray-800/95 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">{{ __('Karyawan') }}</th>
                                <th class="px-4 py-3">{{ __('Jenis') }}</th>
                                <th class="px-4 py-3">{{ __('Tanggal') }}</th>
                                <th class="px-4 py-3">{{ __('Status') }}</th>
                                <th class="px-4 py-3">{{ __('Diproses') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200/80 dark:divide-gray-700/80">
                            @foreach($recentlyProcessed as $leave)
                                <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/60">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <img src="{{ $leave->user->avatar_url }}" alt="{{ $leave->user->name }}" class="h-6 w-6 rounded-full">
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $leave->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ $leave->type_label }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $leave->start_date->format('d M') }} ({{ $leave->duration }} {{ __('hari') }})</td>
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