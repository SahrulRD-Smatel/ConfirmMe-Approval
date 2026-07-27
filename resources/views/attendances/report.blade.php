<x-app-layout>
    <x-slot name="title">Laporan Absensi</x-slot>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="card-elevated p-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-48 h-48 bg-gradient-to-bl from-amber-50/60 to-transparent rounded-full -mr-16 -mt-16 pointer-events-none"></div>
            <div class="relative z-10 flex flex-col gap-4">
                {{-- Title Row --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Laporan Absensi</h1>
                        <p class="text-sm text-gray-500">Rekap absensi seluruh karyawan</p>
                    </div>

                    {{-- Export/Import Buttons --}}
                    <div class="flex flex-wrap items-center gap-2">
                        {{-- Export PDF --}}
                        <a href="{{ route('attendance-admin.export-pdf', ['month' => $month, 'year' => $year, 'department' => $department]) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-600 text-white text-xs font-medium rounded-xl hover:bg-red-700 transition shadow-sm"
                           id="btn-export-pdf">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            PDF
                        </a>

                        {{-- Export CSV --}}
                        <a href="{{ route('attendance-admin.export-csv', ['month' => $month, 'year' => $year, 'department' => $department]) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 text-white text-xs font-medium rounded-xl hover:bg-emerald-700 transition shadow-sm"
                           id="btn-export-csv">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            CSV
                        </a>

                        {{-- Export XLSX --}}
                        <a href="{{ route('attendance-admin.export-xlsx', ['month' => $month, 'year' => $year, 'department' => $department]) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 text-white text-xs font-medium rounded-xl hover:bg-blue-700 transition shadow-sm"
                           id="btn-export-xlsx">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            XLSX
                        </a>

                        {{-- Import Button (admin & hrd only) --}}
                        @if(auth()->user()->hasAnyRole(['admin', 'hrd']))
                            <button type="button"
                                    onclick="document.getElementById('import-modal').classList.remove('hidden')"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-600 text-white text-xs font-medium rounded-xl hover:bg-amber-700 transition shadow-sm"
                                    id="btn-import">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Import
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Filters --}}
                <form method="GET" class="flex flex-wrap items-center gap-2">
                    <select name="month" class="rounded-xl border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                    <select name="year" class="rounded-xl border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                        @for($y = now()->year + 2; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <select name="department" class="rounded-xl border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ $department == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-xl hover:bg-primary-700 transition">
                        Filter
                    </button>
                </form>
            </div>
        </div>

        {{-- Import Summary --}}
        @if(session('import_summary'))
            @php $summary = session('import_summary'); @endphp
            <div class="card-elevated overflow-hidden border border-green-200">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                    <div class="flex items-center gap-2 text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="font-bold text-lg">Import Completed</h3>
                    </div>
                    <p class="text-green-100 text-xs mt-1">{{ $summary['file_name'] ?? 'file' }} ({{ $summary['file_size_kb'] ?? '0' }} KB)</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                        <div class="text-center p-3 bg-gray-50 rounded-xl">
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($summary['total_rows'] ?? 0) }}</div>
                            <div class="text-xs text-gray-500 mt-1 font-medium">Total Rows</div>
                        </div>
                        <div class="text-center p-3 bg-blue-50 rounded-xl">
                            <div class="text-2xl font-bold text-blue-700">{{ number_format($summary['new_records'] ?? 0) }}</div>
                            <div class="text-xs text-blue-600 mt-1 font-medium">New Records</div>
                        </div>
                        <div class="text-center p-3 bg-amber-50 rounded-xl">
                            <div class="text-2xl font-bold text-amber-700">{{ number_format($summary['updated_records'] ?? 0) }}</div>
                            <div class="text-xs text-amber-600 mt-1 font-medium">Updated Records</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-xl">
                            <div class="text-2xl font-bold text-gray-500">{{ number_format($summary['skipped_records'] ?? 0) }}</div>
                            <div class="text-xs text-gray-400 mt-1 font-medium">Skipped Records</div>
                        </div>
                        <div class="text-center p-3 bg-red-50 rounded-xl">
                            <div class="text-2xl font-bold text-red-600">{{ number_format($summary['failed_records'] ?? 0) }}</div>
                            <div class="text-xs text-red-500 mt-1 font-medium">Failed Records</div>
                        </div>
                    </div>

                    @if(session('import_errors') && count(session('import_errors')) > 0)
                        <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                            <p class="text-xs font-semibold text-amber-700 mb-2">Detail baris yang dilewati:</p>
                            <ul class="text-xs text-amber-600 list-disc list-inside space-y-0.5 max-h-32 overflow-y-auto">
                                @foreach(session('import_errors') as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="card-elevated p-4 border-l-4 border-green-500 bg-green-50">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="card-elevated p-4 border-l-4 border-red-500 bg-red-50">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                        @if(session('import_errors') && count(session('import_errors')) > 0 && !session('import_summary'))
                            <ul class="mt-2 text-xs text-red-600 list-disc list-inside space-y-0.5 max-h-32 overflow-y-auto">
                                @foreach(session('import_errors') as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Report Table --}}
        <div class="card-elevated overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                            <th class="px-6 py-3 text-left font-semibold">Karyawan</th>
                            <th class="px-4 py-3 text-center font-semibold">Departemen</th>
                            <th class="px-4 py-3 text-center font-semibold">Hadir</th>
                            <th class="px-4 py-3 text-center font-semibold">Tepat Waktu</th>
                            <th class="px-4 py-3 text-center font-semibold">Terlambat</th>
                            <th class="px-4 py-3 text-center font-semibold">WFH</th>
                            <th class="px-4 py-3 text-center font-semibold">Pulang Awal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($reportData as $data)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $data['user']->avatar_url }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-white shadow-sm">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $data['user']->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $data['user']->position ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">{{ $data['user']->department ?? '-' }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-gray-900">{{ $data['total_days'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-50 text-green-700 font-bold text-xs">
                                        {{ $data['on_time'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $data['late'] > 0 ? 'bg-orange-50 text-orange-700' : 'bg-gray-50 text-gray-400' }} font-bold text-xs">
                                        {{ $data['late'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-700 font-bold text-xs">
                                        {{ $data['wfh'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $data['early_leave'] > 0 ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-50 text-gray-400' }} font-bold text-xs">
                                        {{ $data['early_leave'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-2">
                                        <x-icon name="users" class="w-6 h-6 text-gray-300" />
                                    </div>
                                    <p class="text-sm text-gray-400">Tidak ada data absensi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Import Modal --}}
    @if(auth()->user()->hasAnyRole(['admin', 'hrd']))
        <div id="import-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
                 onclick="document.getElementById('import-modal').classList.add('hidden')"></div>

            {{-- Modal Content --}}
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between p-6 border-b border-gray-100">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Import Data Absensi</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Upload file CSV atau XLSX</p>
                        </div>
                        <button type="button"
                                onclick="document.getElementById('import-modal').classList.add('hidden')"
                                class="rounded-xl p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <form action="{{ route('attendance-admin.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                        @csrf

                        {{-- Info Box --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-xs text-blue-700">
                                    <p class="font-semibold mb-1">Format kolom yang diharapkan:</p>
                                    <code class="text-[11px] bg-blue-100 px-1.5 py-0.5 rounded">employee_id, date, check_in_time, check_out_time, status, is_wfh, notes</code>
                                    <ul class="mt-2 space-y-0.5 text-blue-600">
                                        <li>• Status: <code class="bg-blue-100 px-1 rounded">on_time</code>, <code class="bg-blue-100 px-1 rounded">late</code>, <code class="bg-blue-100 px-1 rounded">wfh</code></li>
                                        <li>• Data yang sudah ada akan <strong>diperbarui/replace</strong></li>
                                        <li>• Format tanggal: <code class="bg-blue-100 px-1 rounded">YYYY-MM-DD</code></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Download Template --}}
                        <a href="{{ route('attendance-admin.import-template') }}"
                           class="inline-flex items-center gap-1.5 text-sm text-primary-600 hover:text-primary-700 font-medium transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download Template CSV
                        </a>

                        {{-- File Upload --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File</label>
                            <div class="relative">
                                <input type="file"
                                       name="file"
                                       accept=".csv,.xlsx,.xls"
                                       required
                                       id="import-file-input"
                                       class="block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2.5 file:px-4
                                              file:rounded-xl file:border-0
                                              file:text-sm file:font-medium
                                              file:bg-primary-50 file:text-primary-700
                                              hover:file:bg-primary-100
                                              cursor-pointer border border-gray-200 rounded-xl
                                              focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <p class="mt-1.5 text-xs text-gray-400">Format: CSV, XLSX — Maksimal 5MB</p>
                            @error('file')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Submit --}}
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button"
                                    onclick="document.getElementById('import-modal').classList.add('hidden')"
                                    class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-5 py-2.5 text-sm font-medium text-white bg-amber-600 rounded-xl hover:bg-amber-700 transition shadow-sm inline-flex items-center gap-2"
                                    id="btn-submit-import">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Import Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
