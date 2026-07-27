<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\OfficeLocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\AuditLog;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display attendance dashboard for the current user (mobile-first).
     */
    public function index()
    {
        $user = auth()->user();
        $today = today();

        $todayCheckIn = Attendance::todayCheckIn($user->id);
        $todayCheckOut = Attendance::todayCheckOut($user->id);
        $workDuration = Attendance::workDurationFormatted($user->id);

        // This month's summary
        $monthAttendances = Attendance::forUser($user->id)
            ->thisMonth()
            ->checkIns()
            ->get();

        $summary = [
            'total_days' => $monthAttendances->count(),
            'on_time' => $monthAttendances->where('status', 'on_time')->count(),
            'late' => $monthAttendances->where('status', 'late')->count(),
            'wfh' => $monthAttendances->where('is_wfh', true)->count(),
        ];

        // Recent 7 days history
        $recentHistory = Attendance::forUser($user->id)
            ->where('date', '>=', now()->subDays(7))
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(fn($a) => $a->date->format('Y-m-d'));

        return view('attendances.index', compact(
            'todayCheckIn', 'todayCheckOut', 'workDuration',
            'summary', 'recentHistory'
        ));
    }

    /**
     * Process check-in.
     */
    public function checkIn(Request $request)
    {
        $user = auth()->user();

        // Prevent double check-in
        if (Attendance::hasCheckedInToday($user->id)) {
            return back()->with('error', 'Anda sudah check-in hari ini.');
        }

        // Validate
        $rules = [
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_wfh' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
        ];

        // Photo is required by config
        if (config('attendance.photo.required', true)) {
            $rules['photo'] = 'required|image|max:' . config('attendance.photo.max_size_kb', 2048);
        } else {
            $rules['photo'] = 'nullable|image|max:' . config('attendance.photo.max_size_kb', 2048);
        }

        // WFH requires notes
        $isWfh = $request->boolean('is_wfh');
        if ($isWfh && config('attendance.wfh.requires_notes', true)) {
            $rules['notes'] = 'required|string|max:500';
        }

        $validated = $request->validate($rules, [
            'photo.required' => 'Foto selfie wajib diambil untuk check-in.',
            'notes.required' => 'Catatan wajib diisi untuk WFH.',
        ]);

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store(
                config('attendance.photo.storage_path', 'attendances'),
                config('attendance.photo.storage_disk', 'public')
            );
        }

        // Determine status
        $status = $this->determineCheckInStatus($request, $isWfh);

        // Find nearest office location (if GPS provided)
        $officeLocationId = null;
        $locationName = null;
        if ($request->filled('latitude') && $request->filled('longitude') && !$isWfh) {
            $nearestOffice = $this->findNearestOffice(
                $request->input('latitude'),
                $request->input('longitude')
            );
            if ($nearestOffice) {
                $officeLocationId = $nearestOffice->id;
                $locationName = $nearestOffice->name;

                // Check if within radius
                if (!$nearestOffice->isWithinRadius($request->input('latitude'), $request->input('longitude'))) {
                    $status = 'invalid_location';
                }
            }
        }

        if ($isWfh) {
            $status = 'wfh';
            $locationName = 'Work From Home';
        }

        Attendance::create([
            'user_id' => $user->id,
            'date' => today(),
            'type' => 'check_in',
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'location_name' => $locationName,
            'office_location_id' => $officeLocationId,
            'photo' => $photoPath,
            'device_info' => $request->userAgent(),
            'status' => $status,
            'is_wfh' => $isWfh,
            'notes' => $request->input('notes'),
        ]);

        $time = now()->format('H:i');
        $statusLabel = $status === 'late' ? ' (Terlambat)' : ($status === 'wfh' ? ' (WFH)' : '');

        return back()->with('success', "Check-in berhasil pada {$time}{$statusLabel}");
    }

    /**
     * Process check-out.
     */
    public function checkOut(Request $request)
    {
        $user = auth()->user();

        // Must check-in first
        if (!Attendance::hasCheckedInToday($user->id)) {
            return back()->with('error', 'Anda belum check-in hari ini.');
        }

        // Prevent double check-out
        if (Attendance::hasCheckedOutToday($user->id)) {
            return back()->with('error', 'Anda sudah check-out hari ini.');
        }

        // Validate
        $rules = [
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'notes' => 'nullable|string|max:500',
        ];

        if (config('attendance.photo.required', true)) {
            $rules['photo'] = 'required|image|max:' . config('attendance.photo.max_size_kb', 2048);
        } else {
            $rules['photo'] = 'nullable|image|max:' . config('attendance.photo.max_size_kb', 2048);
        }

        $validated = $request->validate($rules, [
            'photo.required' => 'Foto selfie wajib diambil untuk check-out.',
        ]);

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store(
                config('attendance.photo.storage_path', 'attendances'),
                config('attendance.photo.storage_disk', 'public')
            );
        }

        // Determine status
        $status = $this->determineCheckOutStatus();
        $todayCheckIn = Attendance::todayCheckIn($user->id);
        $isWfh = $todayCheckIn?->is_wfh ?? false;

        Attendance::create([
            'user_id' => $user->id,
            'date' => today(),
            'type' => 'check_out',
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'location_name' => $isWfh ? 'Work From Home' : null,
            'photo' => $photoPath,
            'device_info' => $request->userAgent(),
            'status' => $isWfh ? 'wfh' : $status,
            'is_wfh' => $isWfh,
            'notes' => $request->input('notes'),
        ]);

        $workDuration = Attendance::workDurationFormatted($user->id);

        return back()->with('success', "Check-out berhasil. Durasi kerja: {$workDuration}");
    }

    /**
     * Display attendance history for the current user.
     */
    public function history(Request $request)
    {
        $user = auth()->user();
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);

        $attendances = Attendance::forUser($user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(fn($a) => $a->date->format('Y-m-d'));

        // Monthly stats
        $checkIns = Attendance::forUser($user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->checkIns()
            ->get();

        $stats = [
            'total' => $checkIns->count(),
            'on_time' => $checkIns->where('status', 'on_time')->count(),
            'late' => $checkIns->where('status', 'late')->count(),
            'wfh' => $checkIns->where('is_wfh', true)->count(),
            'early_leave' => Attendance::forUser($user->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->checkOuts()
                ->where('status', 'early_leave')
                ->count(),
        ];

        return view('attendances.history', compact('attendances', 'stats', 'month', 'year'));
    }

    /**
     * Admin: Monthly attendance report.
     */
    public function report(Request $request)
    {
        $user = auth()->user();

        // Only admin, HRD, or directors can access
        if (!$user->hasAnyRole(['admin', 'hrd', 'hrd_coordinator', 'direktur_hr', 'presiden_direktur', 'manager'])) {
            abort(403, 'Anda tidak memiliki akses ke laporan absensi.');
        }

        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);
        $department = $request->get('department');

        $reportData = $this->getReportData($user, $month, $year, $department);
        $departments = $this->getDepartmentsList();

        return view('attendances.report', compact('reportData', 'month', 'year', 'department', 'departments'));
    }

    /**
     * Admin: Manage office locations.
     */
    public function locations()
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['admin', 'hrd', 'direktur_hr'])) {
            abort(403);
        }

        $locations = OfficeLocation::orderBy('name')->get();

        return view('attendances.locations', compact('locations'));
    }

    /**
     * Admin: Store a new office location.
     */
    public function storeLocation(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['admin', 'hrd', 'direktur_hr'])) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:50|max:5000',
        ]);

        OfficeLocation::create($validated);

        return back()->with('success', 'Lokasi kantor berhasil ditambahkan.');
    }

    /**
     * Export attendance report as PDF.
     */
    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['admin', 'hrd', 'hrd_coordinator', 'direktur_hr', 'presiden_direktur', 'manager'])) {
            abort(403, 'Anda tidak memiliki akses untuk export laporan absensi.');
        }

        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);
        $department = $request->get('department');

        $reportData = $this->getReportData($user, $month, $year, $department);
        $departments = $this->getDepartmentsList();

        // Calculate total stats for summary
        $totalStats = [
            'total_employees' => $reportData->count(),
            'total_on_time' => $reportData->sum('on_time'),
            'total_late' => $reportData->sum('late'),
            'total_wfh' => $reportData->sum('wfh'),
            'total_early_leave' => $reportData->sum('early_leave'),
        ];

        $pdf = Pdf::loadView('attendances.attendance-export', compact(
            'reportData', 'month', 'year', 'department', 'totalStats'
        ));

        $filename = 'laporan-absensi-' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        if ($department) {
            $filename .= '-' . strtolower(str_replace(' ', '-', $department));
        }

        return $pdf->download($filename . '.pdf');
    }

    /**
     * Export attendance report as CSV.
     */
    public function exportCsv(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['admin', 'hrd', 'hrd_coordinator', 'direktur_hr', 'presiden_direktur', 'manager'])) {
            abort(403, 'Anda tidak memiliki akses untuk export laporan absensi.');
        }

        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);
        $department = $request->get('department');

        $reportData = $this->getReportData($user, $month, $year, $department);

        $filename = 'laporan-absensi-' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        if ($department) {
            $filename .= '-' . strtolower(str_replace(' ', '-', $department));
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];

        $callback = function () use ($reportData, $month, $year) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8 compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($handle, [
                'No',
                'Nama Karyawan',
                'Employee ID',
                'Departemen',
                'Posisi',
                'Total Hadir',
                'Tepat Waktu',
                'Terlambat',
                'WFH',
                'Pulang Awal',
            ]);

            // Data rows
            foreach ($reportData as $index => $data) {
                fputcsv($handle, [
                    $index + 1,
                    $data['user']->name,
                    $data['user']->employee_id ?? '-',
                    $data['user']->department ?? '-',
                    $data['user']->position ?? '-',
                    $data['total_days'],
                    $data['on_time'],
                    $data['late'],
                    $data['wfh'],
                    $data['early_leave'],
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export attendance report as XLSX.
     */
    public function exportXlsx(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['admin', 'hrd', 'hrd_coordinator', 'direktur_hr', 'presiden_direktur', 'manager'])) {
            abort(403, 'Anda tidak memiliki akses untuk export laporan absensi.');
        }

        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);
        $department = $request->get('department');

        $reportData = $this->getReportData($user, $month, $year, $department);

        $filename = 'laporan-absensi-' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        if ($department) {
            $filename .= '-' . strtolower(str_replace(' ', '-', $department));
        }

        // Transform data for FastExcel
        $exportData = $reportData->map(function ($data, $index) {
            return [
                'No' => $index + 1,
                'Nama Karyawan' => $data['user']->name,
                'Employee ID' => $data['user']->employee_id ?? '-',
                'Departemen' => $data['user']->department ?? '-',
                'Posisi' => $data['user']->position ?? '-',
                'Total Hadir' => $data['total_days'],
                'Tepat Waktu' => $data['on_time'],
                'Terlambat' => $data['late'],
                'WFH' => $data['wfh'],
                'Pulang Awal' => $data['early_leave'],
            ];
        });

        return (new FastExcel($exportData))->download($filename . '.xlsx');
    }

    /**
     * Import attendance data from CSV/XLSX file.
     *
     * Strategy:
     *   Phase 1 — VALIDATE ALL rows first (zero DB writes).
     *             If any critical errors → abort entirely so data stays consistent.
     *   Phase 2 — BULK UPSERT inside a DB transaction, chunked (500 rows).
     *             Unique key: user_id + date + type
     *             Only updates: status, is_wfh, notes, location_name
     *             Never overwrites: photo, latitude, longitude, device_info
     *   Phase 3 — Write audit log with full import summary.
     *
     * Only accessible by admin and hrd roles.
     */
    public function importData(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['admin', 'hrd'])) {
            abort(403, 'Anda tidak memiliki akses untuk import data absensi.');
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls,txt|max:10240',
        ], [
            'file.required' => 'File wajib diunggah.',
            'file.mimes' => 'Format file harus CSV atau XLSX.',
            'file.max' => 'Ukuran file maksimal 10MB.',
        ]);

        $file = $request->file('file');

        // ── Phase 0: Read file ──────────────────────────────────────────
        try {
            $rows = (new FastExcel)->import($file);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }

        if ($rows->isEmpty()) {
            return back()->with('error', 'File kosong atau tidak memiliki data.');
        }

        // ── Phase 1: VALIDATE ALL — zero DB writes ─────────────────────
        // Pre-load all employees into a lookup map to avoid N+1 queries
        $employeeMap = User::whereNotNull('employee_id')
            ->pluck('id', 'employee_id'); // ['EMP001' => 1, 'EMP002' => 2, ...]

        $validStatuses = ['on_time', 'late', 'early_leave', 'wfh', 'invalid_location'];
        $checkInRecords = [];
        $checkOutRecords = [];
        $errors = [];
        $skipped = 0;
        $totalRows = $rows->count();

        foreach ($rows as $rowIndex => $row) {
            $lineNumber = $rowIndex + 2; // row 1 = header, index starts at 0

            // Normalize column names (case-insensitive, trimmed)
            $row = collect($row)->mapWithKeys(function ($value, $key) {
                return [strtolower(trim($key)) => trim($value ?? '')];
            });

            // Extract fields (support both English and Indonesian column names)
            $employeeId   = $row->get('employee_id') ?? $row->get('employee id') ?? $row->get('id_karyawan') ?? null;
            $date          = $row->get('date') ?? $row->get('tanggal') ?? null;
            $checkInTime   = $row->get('check_in_time') ?? $row->get('check_in') ?? $row->get('jam_masuk') ?? null;
            $checkOutTime  = $row->get('check_out_time') ?? $row->get('check_out') ?? $row->get('jam_keluar') ?? null;
            $status        = $row->get('status') ?? null;
            $notes         = $row->get('notes') ?? $row->get('catatan') ?? null;
            $isWfhRaw      = $row->get('is_wfh') ?? $row->get('wfh') ?? '0';

            // ── Validate required fields
            if (empty($employeeId) || empty($date)) {
                $errors[] = "Baris {$lineNumber}: employee_id dan date wajib diisi.";
                $skipped++;
                continue;
            }

            // ── Resolve employee_id → user_id (from pre-loaded cache)
            $userId = $employeeMap->get($employeeId);
            if (!$userId) {
                $errors[] = "Baris {$lineNumber}: Employee ID '{$employeeId}' tidak ditemukan di database.";
                $skipped++;
                continue;
            }

            // ── Validate date format
            try {
                $parsedDate = Carbon::parse($date)->format('Y-m-d');
            } catch (\Exception $e) {
                $errors[] = "Baris {$lineNumber}: Format tanggal '{$date}' tidak valid (gunakan YYYY-MM-DD).";
                $skipped++;
                continue;
            }

            // ── Must have at least check_in or check_out
            if (empty($checkInTime) && empty($checkOutTime)) {
                $errors[] = "Baris {$lineNumber}: Minimal salah satu dari check_in_time atau check_out_time harus diisi.";
                $skipped++;
                continue;
            }

            // ── Normalize status & WFH
            $normalizedStatus = strtolower($status ?? '');
            if (!in_array($normalizedStatus, $validStatuses)) {
                $normalizedStatus = 'on_time';
            }

            $isWfhBool = in_array(strtolower($isWfhRaw), ['1', 'true', 'ya', 'yes', 'wfh']);
            if ($isWfhBool) {
                $normalizedStatus = 'wfh';
            }

            $locationName = $isWfhBool ? 'Work From Home' : 'Import';
            $now = now();

            // ── Build check-in record
            if (!empty($checkInTime)) {
                $checkInRecords[] = [
                    'user_id'       => $userId,
                    'date'          => $parsedDate,
                    'type'          => 'check_in',
                    'status'        => $normalizedStatus,
                    'is_wfh'        => $isWfhBool,
                    'notes'         => $notes ?: null,
                    'location_name' => $locationName,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
            }

            // ── Build check-out record
            if (!empty($checkOutTime)) {
                $coStatus = $normalizedStatus;

                // Auto-detect early leave from check-out time
                if (!$isWfhBool) {
                    try {
                        $coTime = Carbon::parse($checkOutTime);
                        $workEnd = Carbon::parse(config('attendance.work_hours.end', '17:00'));
                        $threshold = config('attendance.work_hours.early_leave_threshold_minutes', 30);
                        if ($coTime->lt($workEnd->copy()->subMinutes($threshold))) {
                            $coStatus = 'early_leave';
                        }
                    } catch (\Exception $e) {
                        // Keep original status if time parsing fails
                    }
                }

                $checkOutRecords[] = [
                    'user_id'       => $userId,
                    'date'          => $parsedDate,
                    'type'          => 'check_out',
                    'status'        => $isWfhBool ? 'wfh' : $coStatus,
                    'is_wfh'        => $isWfhBool,
                    'notes'         => $notes ?: null,
                    'location_name' => $locationName,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
            }
        }

        // ── Abort if ALL rows are invalid
        $allRecords = array_merge($checkInRecords, $checkOutRecords);
        if (empty($allRecords)) {
            return back()
                ->with('error', 'Import dibatalkan: Tidak ada data valid untuk diproses.')
                ->with('import_errors', array_slice($errors, 0, 20));
        }

        // ── Phase 2: BULK UPSERT inside DB transaction ─────────────────
        $newRecords = 0;
        $updatedRecords = 0;

        try {
            DB::transaction(function () use ($allRecords, &$newRecords, &$updatedRecords) {
                // Count existing records BEFORE upsert to determine new vs updated
                $existingKeys = collect();
                foreach (array_chunk($allRecords, 500) as $chunk) {
                    $conditions = collect($chunk)->map(function ($r) {
                        return [
                            'user_id' => $r['user_id'],
                            'date'    => $r['date'],
                            'type'    => $r['type'],
                        ];
                    });

                    // Query existing records for this chunk
                    $query = Attendance::query();
                    foreach ($conditions as $cond) {
                        $query->orWhere(function ($q) use ($cond) {
                            $q->where('user_id', $cond['user_id'])
                              ->where('date', $cond['date'])
                              ->where('type', $cond['type']);
                        });
                    }

                    $existing = $query->select('user_id', 'date', 'type')->get();
                    foreach ($existing as $e) {
                        $existingKeys->push($e->user_id . '|' . $e->date->format('Y-m-d') . '|' . $e->type);
                    }
                }

                // Count new vs updated
                foreach ($allRecords as $record) {
                    $key = $record['user_id'] . '|' . $record['date'] . '|' . $record['type'];
                    if ($existingKeys->contains($key)) {
                        $updatedRecords++;
                    } else {
                        $newRecords++;
                    }
                }

                // Bulk upsert in chunks of 500
                // Only update these columns for existing records (selective update):
                // status, is_wfh, notes, location_name, updated_at
                // Never overwrite: photo, latitude, longitude, device_info, office_location_id
                foreach (array_chunk($allRecords, 500) as $chunk) {
                    Attendance::upsert(
                        $chunk,
                        ['user_id', 'date', 'type'], // unique key
                        ['status', 'is_wfh', 'notes', 'location_name', 'updated_at'] // columns to update
                    );
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Import gagal — tidak ada data yang diubah. Error: ' . $e->getMessage());
        }

        // ── Phase 3: Audit log ──────────────────────────────────────────
        $summary = [
            'total_rows'       => $totalRows,
            'new_records'      => $newRecords,
            'updated_records'  => $updatedRecords,
            'skipped_records'  => $skipped,
            'failed_records'   => 0,
            'file_name'        => $file->getClientOriginalName(),
            'file_size_kb'     => round($file->getSize() / 1024, 1),
        ];

        AuditLog::log(
            'attendance_imported',
            null,
            [], // no old values
            $summary
        );

        // ── Return with detailed summary ───────────────────────────────
        return back()
            ->with('import_summary', $summary)
            ->with('import_errors', !empty($errors) ? array_slice($errors, 0, 20) : []);
    }

    /**
     * Download CSV template for attendance import.
     */
    public function downloadImportTemplate()
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['admin', 'hrd'])) {
            abort(403);
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template-import-absensi.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8 compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($handle, [
                'employee_id',
                'date',
                'check_in_time',
                'check_out_time',
                'status',
                'is_wfh',
                'notes',
            ]);

            // Example rows
            fputcsv($handle, ['EMP001', '2025-07-01', '08:00', '17:00', 'on_time', '0', '']);
            fputcsv($handle, ['EMP002', '2025-07-01', '08:30', '17:00', 'late', '0', 'Macet']);
            fputcsv($handle, ['EMP003', '2025-07-01', '09:00', '16:00', 'wfh', '1', 'WFH - meeting online']);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ==================== PRIVATE HELPERS ====================

    /**
     * Get report data — shared logic for report view and exports.
     */
    private function getReportData($user, int $month, int $year, ?string $department)
    {
        $usersQuery = User::where('is_active', true)
            ->with(['attendances' => function ($q) use ($month, $year) {
                $q->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->orderBy('date')
                    ->orderBy('created_at');
            }]);

        // Filter by department
        if ($department) {
            $usersQuery->where('department', $department);
        } elseif ($user->hasRole('manager') && !$user->hasAnyRole(['admin', 'hrd', 'direktur_hr'])) {
            $usersQuery->where('department', $user->department);
        }

        $users = $usersQuery->orderBy('name')->get();

        return $users->map(function ($u) {
            $checkIns = $u->attendances->where('type', 'check_in');
            $checkOuts = $u->attendances->where('type', 'check_out');

            return [
                'user' => $u,
                'total_days' => $checkIns->count(),
                'on_time' => $checkIns->where('status', 'on_time')->count(),
                'late' => $checkIns->where('status', 'late')->count(),
                'wfh' => $checkIns->where('is_wfh', true)->count(),
                'early_leave' => $checkOuts->where('status', 'early_leave')->count(),
            ];
        });
    }

    /**
     * Get distinct department list.
     */
    private function getDepartmentsList()
    {
        return User::where('is_active', true)
            ->distinct()
            ->pluck('department')
            ->filter()
            ->sort();
    }

    /**
     * Determine check-in status based on time.
     */
    private function determineCheckInStatus(Request $request, bool $isWfh): string
    {
        if ($isWfh) {
            return 'wfh';
        }

        $now = now();
        $workStart = Carbon::parse(config('attendance.work_hours.start', '08:00'));
        $tolerance = config('attendance.work_hours.late_tolerance_minutes', 15);

        if ($now->lte($workStart->addMinutes($tolerance))) {
            return 'on_time';
        }

        return 'late';
    }

    /**
     * Determine check-out status based on time.
     */
    private function determineCheckOutStatus(): string
    {
        $now = now();
        $workEnd = Carbon::parse(config('attendance.work_hours.end', '17:00'));
        $threshold = config('attendance.work_hours.early_leave_threshold_minutes', 30);

        if ($now->lt($workEnd->subMinutes($threshold))) {
            return 'early_leave';
        }

        return 'on_time';
    }

    /**
     * Find the nearest active office location.
     */
    private function findNearestOffice(float $lat, float $lng): ?OfficeLocation
    {
        $offices = OfficeLocation::active()->get();

        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($offices as $office) {
            $distance = $office->distanceTo($lat, $lng);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = $office;
            }
        }

        return $nearest;
    }
}
