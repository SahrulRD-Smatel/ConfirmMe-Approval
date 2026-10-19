<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen - PT INFO SOLUSINDO DATA UTAMA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="max-w-2xl mx-auto py-12 px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">PT INFO SOLUSINDO DATA UTAMA</h1>
            <p class="text-gray-600 mt-1">Sistem Verifikasi Dokumen Digital</p>
        </div>

        @if(!$valid)
            <!-- Invalid Document -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Dokumen Tidak Valid</h2>
                <p class="text-gray-600">{{ $message }}</p>
            </div>
        @else
            <!-- Valid Document -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Status Banner -->
                @if($isFullyApproved)
                    <div class="bg-green-500 text-white px-6 py-4 flex items-center gap-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <div>
                            <h2 class="text-lg font-bold">{{ __('DOKUMEN SAH') }}</h2>
                            <p class="text-sm opacity-90">{{ __('Telah diverifikasi dan disetujui secara digital') }}</p>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-500 text-white px-6 py-4 flex items-center gap-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h2 class="text-lg font-bold">{{ __('DALAM PROSES') }}</h2>
                            <p class="text-sm opacity-90">{{ __('Dokumen sedang dalam proses persetujuan') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Document Info -->
                <div class="p-6 border-b">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nomor Dokumen</p>
                            <p class="font-semibold text-gray-900">{{ $document->document_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Jenis</p>
                            <p class="font-semibold text-gray-900">{{ $document->approval_type_label }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500">Judul</p>
                            <p class="font-semibold text-gray-900">{{ $document->title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Pengaju</p>
                            <p class="font-semibold text-gray-900">{{ $document->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Pengajuan</p>
                            <p class="font-semibold text-gray-900">{{ $document->created_at->format('d M Y, H:i') }} WIB</p>
                        </div>
                        @if($document->approved_budget)
                            <div>
                                <p class="text-sm text-gray-500">Nilai Disetujui</p>
                                <p class="font-semibold text-gray-900">Rp
                                    {{ number_format($document->approved_budget, 0, ',', '.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Approval Chain -->
                <div class="p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Rantai Persetujuan Digital</h3>
                    <div class="space-y-3">
                        @foreach($approvals as $approval)
                            <div
                                class="flex items-center gap-4 p-3 rounded-lg {{ $approval->status === 'approved' ? 'bg-green-50' : ($approval->status === 'rejected' ? 'bg-red-50' : 'bg-gray-50') }}">
                                <!-- Status Icon -->
                                <div class="flex-shrink-0">
                                    @if($approval->status === 'approved')
                                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    @elseif($approval->status === 'rejected')
                                        <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900">{{ $approval->approver->name ?? 'Unknown' }}</p>
                                    <p class="text-sm text-gray-500">{{ $approval->approver?->position ?? $approval->role_label }}</p>
                                </div>
                                <!-- Time -->
                                <div class="text-right text-sm">
                                    @if($approval->action_at)
                                        <p class="text-gray-900">{{ $approval->action_at->format('d/m/Y') }}</p>
                                        <p class="text-gray-500">{{ $approval->action_at->format('H:i') }} WIB</p>
                                    @else
                                        <p class="text-gray-400">Menunggu</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Final Approval Info -->
                @if($isFullyApproved && $finalApproval)
                    <div class="px-6 pb-6">
                        <div class="bg-green-700 rounded-lg p-4 text-white">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <div>
                                    <p class="font-semibold">Final Approval oleh
                                        {{ $finalApproval->approver->name ?? 'Presiden Direktur' }}</p>
                                    <p class="text-sm opacity-90">{{ $finalApproval->action_at?->format('d F Y, H:i') }} WIB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="text-center mt-6 text-sm text-gray-500">
                <p>Dokumen ini diproses melalui sistem digital PT INFO SOLUSINDO DATA UTAMA.</p>
                <p class="mt-1">Scan QR Code pada dokumen resmi untuk memverifikasi keaslian.</p>
            </div>
        @endif
    </div>
</body>

</html>
