<x-app-layout>
    <x-slot name="title">Laporan Cuti</x-slot>

    <div class="space-y-7">
        {{-- Header --}}
        <div class="card-elevated relative overflow-hidden rounded-3xl p-6">
            <div class="absolute top-0 right-0 w-48 h-48 bg-gradient-to-bl from-amber-50/60 to-transparent rounded-full -mr-16 -mt-16 pointer-events-none"></div>
            <div class="relative z-10 flex flex-col gap-4">
                {{-- Title Row --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Laporan Cuti</h1>
                        <p class="text-sm text-gray-500">Rekapitulasi pengajuan cuti yang disetujui</p>
                    </div>
                </div>

                {{-- Filters --}}
                <form method="GET" class="flex flex-wrap items-center gap-2">
                    <select name="month" class="rounded-xl border-gray-300 text-sm focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20/10">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                    <select name="year" class="rounded-xl border-gray-300 text-sm focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20/10">
                        @for($y = now()->year + 2; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <select name="department" class="rounded-xl border-gray-300 text-sm focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20/10">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ $department == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                    <select name="type" class="rounded-xl border-gray-300 text-sm focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20/10">
                        <option value="">Semua Tipe Cuti</option>
                        <option value="annual" {{ $type == 'annual' ? 'selected' : '' }}>Cuti Tahunan</option>
                        <option value="sick" {{ $type == 'sick' ? 'selected' : '' }}>Sakit</option>
                        <option value="unpaid" {{ $type == 'unpaid' ? 'selected' : '' }}>Tanpa Gaji</option>
                        <option value="other" {{ $type == 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-xl hover:bg-primary-700 transition">
                        Filter
                    </button>
                </form>
            </div>
        </div>

        {{-- Report Table --}}
        <div class="card-elevated overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-[760px] w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/80 text-xs uppercase tracking-[0.14em] text-gray-500">
                            <th class="px-6 py-3 text-left font-semibold">Karyawan</th>
                            <th class="px-4 py-3 text-left font-semibold">Tipe Cuti</th>
                            <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                            <th class="px-4 py-3 text-center font-semibold">Durasi</th>
                            <th class="px-6 py-3 text-left font-semibold">Alasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($leaveRequests as $req)
                            <tr class="hover:bg-primary-50/60 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $req->user->avatar_url }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-white shadow-soft">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $req->user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $req->user->department ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $req->type_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-gray-900">{{ \Carbon\Carbon::parse($req->start_date)->translatedFormat('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">s/d {{ \Carbon\Carbon::parse($req->end_date)->translatedFormat('d M Y') }}</div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="font-bold text-gray-900">{{ $req->duration }} Hari</span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600 line-clamp-2" title="{{ $req->reason }}">{{ $req->reason }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-2">
                                        <x-icon name="calendar" class="w-6 h-6 text-gray-300" />
                                    </div>
                                    <p class="text-sm text-gray-400">Tidak ada pengajuan cuti yang disetujui pada periode ini</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($leaveRequests->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $leaveRequests->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
