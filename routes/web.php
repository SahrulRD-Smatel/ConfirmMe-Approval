<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ReportController;

use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\Finance\BudgetController;
use App\Http\Controllers\Finance\PurchaseOrderController;
use App\Http\Controllers\Finance\InvoiceController;
use App\Http\Controllers\Finance\LoanController;
use App\Http\Controllers\Finance\DashboardController as FinanceDashboardController;
use App\Http\Controllers\MyLoanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\WorkflowController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Document Verification (Public - for QR code scanning)
Route::get('/verify/{hash}', [VerificationController::class, 'show'])->name('verify.show');



// Account Pending Route (auth only — no role/active check needed here)
Route::middleware(['auth'])->group(function () {
    Route::get('/account/pending', function () {
        // If user already has roles, redirect to dashboard
        if (auth()->user()->roles->isNotEmpty()) {
            return redirect()->route('dashboard');
        }
        return view('auth.account-pending');
    })->name('account.pending');
});

// Authenticated Routes
Route::middleware(['auth', 'verified', 'active', 'has_role'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Search
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
    Route::patch('/profile/bank', [ProfileController::class, 'updateBank'])->name('profile.bank.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
    });

    // Leave Requests (All authenticated users)
    Route::resource('leave-requests', \App\Http\Controllers\LeaveRequestController::class)
        ->except(['edit', 'update', 'destroy']);

    // Leave Approval (Supervisor for staff, HR Manager/Director for higher roles)
    Route::prefix('leave-approvals')->name('leave-approvals.')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\LeaveApprovalController::class, 'index'])->name('index');
            Route::post('/{leaveRequest}/approve', [\App\Http\Controllers\LeaveApprovalController::class, 'approve'])->name('approve');
            Route::post('/{leaveRequest}/reject', [\App\Http\Controllers\LeaveApprovalController::class, 'reject'])->name('reject');
        });


    // HRIS: Announcements (Authorization handled in Controller)
    Route::resource('announcements', \App\Http\Controllers\AnnouncementController::class);
    Route::resource('documents', DocumentController::class);
    Route::get('/documents/template-data/{code}', [DocumentController::class, 'getTemplate'])->name('documents.template-data');
    Route::post('/documents/{document}/submit', [DocumentController::class, 'submit'])->name('documents.submit');
    Route::post('/documents/{document}/comment', [DocumentController::class, 'comment'])->name('documents.comment');
    Route::get('/documents/{document}/print', [DocumentController::class, 'print'])->name('documents.print');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::post('/documents/{document}/confirm-amount', [DocumentController::class, 'confirmAmount'])->name('documents.confirm-amount');
    Route::post('/documents/{document}/reject-amount', [DocumentController::class, 'rejectAmount'])->name('documents.reject-amount');

    // Attachment Download (using Storage facade for Windows compatibility)
    Route::get('/attachments/{attachment}/download', [DocumentController::class, 'downloadAttachment'])->name('attachments.download');

    // Archive
    Route::get('/archive', [ArchiveController::class, 'index'])->name('archive.index');
    Route::get('/archive/{document}', [ArchiveController::class, 'show'])->name('archive.show');

    // Approvals (For all approvers - dynamic routing handled in controller)
    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])->name('index');
        Route::get('/history', [ApprovalController::class, 'history'])->name('history');
        Route::get('/bulk', [ApprovalController::class, 'bulk'])->name('bulk');
        Route::post('/bulk', [ApprovalController::class, 'bulkProcess'])->name('bulk.process');
        Route::get('/{document}', [ApprovalController::class, 'show'])->name('show');
        Route::post('/{document}/approve', [ApprovalController::class, 'approve'])->name('approve');
        Route::post('/{document}/reject', [ApprovalController::class, 'reject'])->name('reject');
        Route::post('/{document}/revise', [ApprovalController::class, 'revise'])->name('revise');
        Route::post('/{document}/comment', [ApprovalController::class, 'comment'])->name('comment');
    });

    // 2FA Verification (For Division Directors and Presiden Direktur)
    Route::prefix('2fa')->name('2fa.')->group(function () {
        Route::get('/{document}', [TwoFactorController::class, 'show'])->name('show');
        Route::post('/{document}/verify', [TwoFactorController::class, 'verify'])->name('verify');
        Route::post('/{document}/resend', [TwoFactorController::class, 'resend'])->name('resend');
    });

    // Team Documents (For supervisors and managers)
    Route::prefix('team')->name('team.')->group(function () {
        Route::get('/documents', [TeamController::class, 'documents'])->name('documents');
    });

    // Reports (Role-based - Management level only)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/documents', [ReportController::class, 'documents'])->name('documents');
        Route::get('/finance', [ReportController::class, 'finance'])->name('finance');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
    });

    // Finance Routes (Role-based - Finance only)
    Route::prefix('finance')->name('finance.')->middleware('role:finance')->group(function () {
        // Dashboard
        Route::get('/', [FinanceDashboardController::class, 'index'])->name('dashboard');

        // Budgets
        Route::resource('budgets', BudgetController::class);

        // Purchase Orders
        Route::resource('po', PurchaseOrderController::class);
        Route::get('/po/{po}/print', [PurchaseOrderController::class, 'print'])->name('po.print');
        Route::post('/po/{po}/issue', [PurchaseOrderController::class, 'issue'])->name('po.issue');

        // Invoices
        Route::resource('invoices', InvoiceController::class);
        Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        Route::post('/invoices/{invoice}/payment', [InvoiceController::class, 'recordPayment'])->name('invoices.payment');
        Route::post('/invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
        Route::post('/po/{po}/create-invoice', [InvoiceController::class, 'createFromPO'])->name('invoices.from-po');

        // Employee Loans
        Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
        Route::get('/loans/reports/receivables', [LoanController::class, 'receivablesReport'])->name('loans.receivables');
        Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
        Route::post('/loans/{loan}/disburse', [LoanController::class, 'disburse'])->name('loans.disburse');
        Route::post('/loans/{loan}/repayment', [LoanController::class, 'recordRepayment'])->name('loans.repayment');
    });

    // Admin Routes (Role-based - Admin only)
    Route::prefix('admin')->name('admin.')->group(function () {
        // User Management
        Route::resource('users', UserController::class)->middleware('can:manage-users');
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle')->middleware('can:manage-users');

        // Role & Permission Management
        Route::resource('roles', RoleController::class)->middleware('can:manage-roles');

        // System Features Management
        Route::prefix('features')->name('features.')->middleware('can:manage-features')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SystemFeatureController::class, 'index'])->name('index');
            Route::patch('/{key}/toggle', [\App\Http\Controllers\Admin\SystemFeatureController::class, 'toggle'])->name('toggle');
        });

        // Workflow Configuration (Dynamic)
        Route::prefix('workflow')->name('workflow.')->middleware('can:manage-workflow')->group(function () {
            Route::get('/', [WorkflowController::class, 'index'])->name('index');
            Route::post('/type-access', [WorkflowController::class, 'updateTypeAccess'])->name('type-access.update');
            Route::post('/settings', [WorkflowController::class, 'updateSettings'])->name('settings.update');
            Route::post('/reset', [WorkflowController::class, 'resetToDefault'])->name('reset');
        });

        // Departments
        Route::resource('departments', DepartmentController::class)->middleware('can:manage-departments');
        Route::patch('/departments/{department}/toggle', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle')->middleware('can:manage-departments');

        // Document Templates
        Route::resource('templates', TemplateController::class)->middleware('can:manage-templates');
        Route::post('/templates/{template}/duplicate', [TemplateController::class, 'duplicate'])->name('templates.duplicate')->middleware('can:manage-templates');

        // Audit Logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index')->middleware('can:view-audit-log');
        Route::get('/audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export')->middleware('can:view-audit-log');
        Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show')->middleware('can:view-audit-log');
    });

    // Executive Routes (Presiden Direktur only)
    Route::prefix('executive')->name('executive.')->middleware('role:presiden_direktur')->group(function () {
        Route::get('/', [\App\Http\Controllers\ExecutiveController::class, 'dashboard'])->name('dashboard');
        Route::get('/approvals', [\App\Http\Controllers\ExecutiveController::class, 'approvals'])->name('approvals');
        Route::get('/approvals/{document}', [\App\Http\Controllers\ExecutiveController::class, 'showApproval'])->name('approvals.show');
        Route::post('/approvals/{document}/approve', [\App\Http\Controllers\ExecutiveController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{document}/reject', [\App\Http\Controllers\ExecutiveController::class, 'reject'])->name('approvals.reject');
        Route::post('/approvals/{document}/revise', [\App\Http\Controllers\ExecutiveController::class, 'revise'])->name('approvals.revise');
        Route::get('/insights', [\App\Http\Controllers\ExecutiveController::class, 'insights'])->name('insights');
        Route::get('/audit', [\App\Http\Controllers\ExecutiveController::class, 'audit'])->name('audit');
    });

    // My Loans (Employee view of their own loans)
    Route::get('/my-loans', [MyLoanController::class, 'index'])->name('my-loans.index');
    Route::get('/my-loans/{loan}', [MyLoanController::class, 'show'])->name('my-loans.show');

    // Attendance (All authenticated users)
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('check-in');
        Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('check-out');
        Route::get('/history', [AttendanceController::class, 'history'])->name('history');
    });

    // Attendance Admin (HRD, Admin, Directors)
    Route::prefix('attendance-admin')->name('attendance-admin.')->middleware('role:admin|hrd|hrd_coordinator|direktur_hr|presiden_direktur|manager')->group(function () {
        Route::get('/report', [AttendanceController::class, 'report'])->name('report');
        Route::get('/report/export-pdf', [AttendanceController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/report/export-csv', [AttendanceController::class, 'exportCsv'])->name('export-csv');
        Route::get('/report/export-xlsx', [AttendanceController::class, 'exportXlsx'])->name('export-xlsx');
        Route::post('/report/import', [AttendanceController::class, 'importData'])->name('import');
        Route::get('/report/import-template', [AttendanceController::class, 'downloadImportTemplate'])->name('import-template');
        Route::get('/locations', [AttendanceController::class, 'locations'])->name('locations');
        Route::post('/locations', [AttendanceController::class, 'storeLocation'])->name('locations.store');
    });
    // Leave Reports
    Route::prefix('leave-reports')->name('leave-reports.')->middleware('role:admin|hrd|hrd_coordinator|direktur_hr|presiden_direktur|manager')->group(function () {
        Route::get('/', [\App\Http\Controllers\LeaveReportController::class, 'index'])->name('index');
    });
});

require __DIR__ . '/auth.php';
