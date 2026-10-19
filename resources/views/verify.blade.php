<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Verifikasi Dokumen') }} - PT Info Solusindo Data Utama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; }
        .anim-fade-up { opacity: 0; animation: fade-up 0.55s cubic-bezier(0.22, 0.61, 0.36, 1) forwards; }
        @keyframes fade-up {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (prefers-reduced-motion: reduce) {
            .anim-fade-up { animation: none; opacity: 1; }
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen antialiased">
    {{-- Brand bar --}}
    <div class="bg-[#82181a]">
        <div class="mx-auto max-w-2xl px-4 py-4 sm:px-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white">
                    <svg class="h-6 w-6 text-[#82181a]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-white">PT Info Solusindo Data Utama</p>
                    <p class="text-xs text-white/70">{{ __('Verifikasi Keaslian Dokumen') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 sm:py-10">

        @if(!$valid)
            {{-- Invalid document --}}
            <div class="anim-fade-up overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="bg-red-700 px-6 py-5">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white">
                            <svg class="h-6 w-6 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-white">{{ __('DOKUMEN TIDAK VALID') }}</h2>
                            <p class="text-sm text-white/80">{{ __('Kode verifikasi tidak dikenali sistem') }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <p class="text-center text-sm leading-relaxed text-gray-600">{{ $message }}</p>
                    <p class="mt-4 text-center text-xs text-gray-400">
                        {{ __('Pastikan Anda memindai QR Code dari dokumen resmi yang diterbitkan perusahaan.') }}
                    </p>
                </div>
            </div>
        @else
            {{-- Valid document --}}
            <div class="anim-fade-up overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                {{-- Status banner --}}
                @if($isFullyApproved)
                    <div class="bg-green-700 px-6 py-5">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white">
                                <svg class="h-6 w-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-white">{{ __('DOKUMEN SAH') }}</h2>
                                <p class="text-sm text-white/80">{{ __('Telah diverifikasi dan disetujui secara digital') }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-amber-500 px-6 py-5">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white">
                                <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-white">{{ __('DALAM PROSES') }}</h2>
                                <p class="text-sm text-white/85">{{ __('Dokumen sedang dalam proses persetujuan') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Document details --}}
                <div class="border-b border-gray-100 p-6 sm:p-8">
                    <h3 class="text-xs font-bold uppercase tracking-[0.14em] text-gray-400">{{ __('Detail Dokumen') }}</h3>
                    <dl class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs text-gray-500">{{ __('Nomor Dokumen') }}</dt>
                            <dd class="mt-1 font-mono text-sm font-semibold text-gray-900">{{ $document->document_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">{{ __('Jenis Dokumen') }}</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $document->approval_type_label }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs text-gray-500">{{ __('Judul') }}</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $document->title }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">{{ __('Pengaju') }}</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $document->user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">{{ __('Tanggal Pengajuan') }}</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $document->created_at->format('d M Y, H:i') }} WIB</dd>
                        </div>
                        @if($document->approved_budget)
                            <div class="sm:col-span-2">
                                <dt class="text-xs text-gray-500">{{ __('Nilai Disetujui') }}</dt>
                                <dd class="mt-1 text-base font-bold text-gray-900">Rp {{ number_format($document->approved_budget, 0, ',', '.') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Approval timeline --}}
                <div class="p-6 sm:p-8">
                    <h3 class="text-xs font-bold uppercase tracking-[0.14em] text-gray-400">{{ __('Rantai Persetujuan Digital') }}</h3>
                    <ol class="mt-5 space-y-0">
                        @foreach($approvals as $approval)
                            <li class="relative flex gap-4 pb-6 last:pb-0">
                                @if(!$loop->last)
                                    <span class="absolute left-[19px] top-10 h-[calc(100%-2.5rem)] w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative z-10 flex h-10 w-10 shrink-0 items-center justify-center rounded-full
                                    {{ $approval->status === 'approved' ? 'bg-green-600' : ($approval->status === 'rejected' ? 'bg-red-600' : 'bg-gray-300') }}">
                                    @if($approval->status === 'approved')
                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @elseif($approval->status === 'rejected')
                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1 pt-0.5">
                                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-gray-900">{{ $approval->approver->name ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500">{{ $approval->approver?->position ?? $approval->role_label }}</p>
                                        </div>
                                        <div class="shrink-0 text-right">
                                            @if($approval->action_at)
                                                <p class="text-xs font-medium text-gray-700">{{ $approval->action_at->format('d/m/Y') }}</p>
                                                <p class="text-xs text-gray-400">{{ $approval->action_at->format('H:i') }} WIB</p>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-500">{{ __('Menunggu') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs font-medium
                                        {{ $approval->status === 'approved' ? 'text-green-700' : ($approval->status === 'rejected' ? 'text-red-700' : 'text-gray-400') }}">
                                        {{ $approval->status === 'approved' ? __('Disetujui') : ($approval->status === 'rejected' ? __('Ditolak') : __('Menunggu persetujuan')) }}
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>

                {{-- Final approval --}}
                @if($isFullyApproved && $finalApproval)
                    <div class="px-6 pb-6 sm:px-8 sm:pb-8">
                        <div class="rounded-xl bg-green-700 p-4 sm:p-5">
                            <div class="flex items-center gap-3">
                                <svg class="h-8 w-8 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-white">{{ __('Persetujuan final oleh') }} {{ $finalApproval->approver->name ?? __('Presiden Direktur') }}</p>
                                    <p class="mt-0.5 text-xs text-white/80">{{ $finalApproval->action_at?->format('d F Y, H:i') }} WIB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <p class="anim-fade-up mt-6 text-center text-xs leading-relaxed text-gray-400" style="animation-delay:0.15s">
            {{ __('Dokumen ini diproses melalui sistem digital PT Info Solusindo Data Utama.') }}<br>
            {{ __('Pindai QR Code pada dokumen resmi untuk memverifikasi keaslian.') }}
        </p>
    </div>
</body>

</html>
