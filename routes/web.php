<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\eco\manager\BookController;
use App\Http\Controllers\eco\manager\CreditController;
use App\Http\Controllers\hrm\employee\AttendanceController;
use App\Http\Controllers\hrm\employee\HrmstaffpayrollController;
use App\Http\Controllers\hrm\employee\InterviewController;
use App\Http\Controllers\hrm\employee\LeaveController;
use App\Http\Controllers\hrm\employee\TrainingController;
use App\Http\Controllers\hrm\manager\AnalyticsController;
use App\Http\Controllers\hrm\manager\ApplicantController;
use App\Http\Controllers\hrm\manager\OnboardingController;
use App\Http\Controllers\hrm\manager\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\scm\employee\InboundController;
use App\Http\Controllers\scm\employee\InventoryController;
use App\Http\Controllers\scm\employee\RecievingController;
use App\Http\Controllers\scm\employee\VerificationController;
use App\Http\Controllers\scm\manager\AuditController;
use App\Http\Controllers\scm\manager\CloseController;
use App\Http\Controllers\scm\manager\SourcingController;
use App\Http\Controllers\users\ClockController;
use App\Http\Controllers\users\leaveController as UserLeaveController;
use App\Http\Controllers\trainee\TraineeDashboardController;
use App\Http\Controllers\trainee\TraineeTimeKeepingController;
use App\Http\Controllers\trainee\TraineeAttendanceController;
use App\Http\Controllers\trainee\TraineePayslipController;
use App\Http\Controllers\finance\FinanceDashboardController;
use App\Http\Controllers\finance\FinanceTransactionController;
use App\Http\Controllers\finance\FinanceInvoiceBillController;
use App\Http\Controllers\finance\FinanceReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return inertia('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => app()->version(),
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth'])->group(function () {
    // The main entry point that redirects based on user role/position
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    // NEW: Specific route for the Employee Login UI (pointing to USERS/app.vue)
    // routes/web.php

});

/*
|--------------------------------------------------------------------------
| Employee Unified Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'position:staff,manager'])->prefix('dashboard/employee-ui')->group(function () {

    // Your main app.vue page
    Route::get('/', function () {
        return inertia('Dashboard/USERS/app');
    })->name('employee.ui.dashboard');

    // New Page: Schedule
    // Updated to use the Controller for the GET request
    Route::get('/clock', [ClockController::class, 'clock'])->name('employee.ui.clock');

    // NEW: The POST route to handle the actual button click
    Route::post('/clock/toggle', [ClockController::class, 'toggle'])->name('employee.attendance.toggle');

    // New Page: Tasks
    // GET: Display the leave page and fetch history from the controller
    Route::get('/leave', [UserLeaveController::class, 'leave'])->name('employee.ui.leave');

    // POST: Submit the form and save to database
    Route::post('/leave', [UserLeaveController::class, 'store'])->name('employee.leave.store');

    Route::get('/payslip', function () {
        return inertia('Dashboard/USERS/payslip');
    })->name('employee.ui.payslip');

});

/*
|--------------------------------------------------------------------------
| Trainee Unified Routes
|--------------------------------------------------------------------------
*/
Route::prefix('trainee')->middleware(['auth', 'position:trainee'])->group(function () {
    // Trainee Dashboard
    Route::get('/dashboard', [TraineeDashboardController::class, 'index'])->name('trainee.dashboard');
    
    // Time Keeping / Clock In/Out
    Route::get('/timekeeping', [TraineeTimeKeepingController::class, 'index'])->name('trainee.timekeeping');
    Route::post('/timekeeping/clock', [TraineeTimeKeepingController::class, 'clockInOut'])->name('trainee.timekeeping.clock');
    
    // Attendance Records
    Route::get('/attendance', [TraineeAttendanceController::class, 'index'])->name('trainee.attendance');
    
    // Payslips
    Route::get('/payslip', [TraineePayslipController::class, 'index'])->name('trainee.payslip');
    Route::get('/payslip/{payroll}', [TraineePayslipController::class, 'show'])->name('trainee.payslip.show');
});

/*
|--------------------------------------------------------------------------
| HRM Department Routes
|--------------------------------------------------------------------------
*/
Route::prefix('dashboard/hrm')->name('hrm.')->middleware(['auth', 'verified'])->group(function () {

    // Pointing to consolidated index logic for HRM Staff
    Route::get('/employee', [DashboardController::class, 'index'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('employee.dashboard');

    Route::get('/training', [TrainingController::class, 'training'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('employee.training');

    // New Grading Route
    Route::post('/training/grade/{id}', [TrainingController::class, 'submitGrade'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('training.grade');

    /**
     * PROMOTION SUGGESTION ROUTE (Staff Side)
     * This connects the Training.vue modal to TrainingController@suggestPromotion
     */
    Route::post('/training/suggest-promotion/{id}', [TrainingController::class, 'suggestPromotion'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('training.suggest-promotion');

    Route::get('/leave', [LeaveController::class, 'leave'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('employee.leave');

    // Route for Approve/Reject buttons (PATCH)
    Route::patch('/leave/{leaveRequest}', [LeaveController::class, 'update'])->name('employee.leave.update');

    // Route for the Manual Entry form (POST)
    Route::post('/leave/manual', [LeaveController::class, 'store_manual'])->name('employee.leave.store_manual');

    Route::get('/attendance', [AttendanceController::class, 'attendance'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('employee.attendance');

    // Inside web.php
    Route::post('/attendance/toggle', [App\Http\Controllers\hrm\employee\AttendanceController::class, 'toggle'])
        ->name('employee.attendance.toggle');

    Route::post('/attendance/update-shift', [AttendanceController::class, 'updateShift'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('employee.attendance.update-shift');

    Route::get('/interview', [InterviewController::class, 'interview'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('employee.interview');

    Route::post('/InterviewController/update-status', [InterviewController::class, 'submitStatus'])
        ->middleware(['role:HRM', 'position:staff']);

    // Inside the hrm prefix group...
    Route::post('/interview/{interview}/evaluate', [InterviewController::class, 'updateStatus'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('employee.interview.evaluate');

    // Inside your hrm prefix and employee/staff middleware group:
    Route::post('/interview/{interview}/reschedule', [InterviewController::class, 'reschedule'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('employee.interview.reschedule');

    // --- HRM Manager Routes ---
    Route::get('/manager', [DashboardController::class, 'index'])
        ->middleware(['role:HRM', 'position:manager'])
        ->name('manager.dashboard');

    Route::get('/hrmstaffpayroll', [HrmstaffpayrollController::class, 'hrmstaffpayroll'])
        ->middleware(['role:HRM', 'position:staff'])
        ->name('employee.hrmstaffpayroll');

    Route::post('/payroll/store', [hrmstaffpayrollController::class, 'store'])
        ->name('hrm.employee.payroll.store');

    // routes/web.php inside the hrm. prefix group

    Route::patch('/payroll/{id}/approve', [HrmstaffpayrollController::class, 'approve'])
        ->name('employee.payroll.approve');

    Route::patch('/payroll/{id}/reject', [HrmstaffpayrollController::class, 'reject'])
        ->name('employee.payroll.reject');

    Route::post('/holidays/store', [hrmstaffpayrollController::class, 'storeHoliday'])
        ->name('hrm.holidays.store');

    /**
     * FINALIZE PROMOTION ROUTE (Manager Side)
     * This allows the Manager to approve the suggestion and upgrade Trainee to Staff
     */
    Route::post('/manager/finalize-promotion/{id}', [DashboardController::class, 'finalizePromotion'])
        ->middleware(['role:HRM', 'position:manager'])
        ->name('manager.finalize-promotion');

    // Updated Applicant Routes
    Route::controller(ApplicantController::class)->group(function () {
        Route::get('/applicants', 'index')
            ->middleware(['role:HRM', 'position:manager,staff'])
            ->name('applicants.index');

        Route::post('/applicants', 'store')
            ->middleware(['role:HRM', 'position:manager,staff'])
            ->name('applicants.store');

        Route::post('/applicants/{applicant}/schedule', 'scheduleInterview')
            ->middleware(['role:HRM', 'position:manager,staff'])
            ->name('applicants.schedule');

        Route::patch('/applicants/{applicant}/update-stage', 'updateStage')
            ->middleware(['role:HRM', 'position:manager,staff'])
            ->name('applicants.update-stage');

        Route::post('/applicants/{applicant}/create-user', 'createUser')
            ->middleware(['role:HRM', 'position:manager,staff'])
            ->name('applicants.create-user');
    });

    // --- Onboarding & Pipeline Routes ---
    Route::controller(OnboardingController::class)->group(function () {
        Route::get('/onboarding', 'onboarding')
            ->middleware(['role:HRM', 'position:manager'])
            ->name('manager.onboarding');
    });

    Route::get('/payroll', [PayrollController::class, 'payroll'])
        ->middleware(['role:HRM', 'position:manager'])
        ->name('manager.payroll');

    Route::get('/analytics', [AnalyticsController::class, 'analytics'])
        ->middleware(['role:HRM', 'position:manager'])
        ->name('manager.analytics');
});

/*
|--------------------------------------------------------------------------
| SCM Department Routes
|--------------------------------------------------------------------------
*/
Route::prefix('dashboard/scm')->name('scm.')->middleware(['auth', 'verified'])->group(function () {

    Route::get('/manager', [DashboardController::class, 'index'])
        ->middleware(['role:SCM', 'position:manager'])
        ->name('manager.dashboard');

    Route::get('/sourcing', [SourcingController::class, 'sourcing'])
        ->middleware(['role:SCM', 'position:manager'])
        ->name('manager.sourcing');

    Route::get('/audit', [AuditController::class, 'audit'])
        ->middleware(['role:SCM', 'position:manager'])
        ->name('manager.audit');

    Route::get('/close', [CloseController::class, 'close'])
        ->middleware(['role:SCM', 'position:manager'])
        ->name('manager.close');

    Route::get('/staff', [DashboardController::class, 'index'])
        ->middleware(['role:SCM', 'position:staff'])
        ->name('employee.dashboard');

    Route::get('/inbound', [InboundController::class, 'inbound'])
        ->middleware(['role:SCM', 'position:staff'])
        ->name('employee.inbound');

    Route::get('/inventory', [InventoryController::class, 'inventory'])
        ->middleware(['role:SCM', 'position:staff'])
        ->name('employee.inventory');

    Route::get('/recieving', [RecievingController::class, 'recieving'])
        ->middleware(['role:SCM', 'position:staff'])
        ->name('employee.recieving');

    Route::get('/verification', [VerificationController::class, 'verification'])
        ->middleware(['role:SCM', 'position:staff'])
        ->name('employee.verification');
});

/*
|--------------------------------------------------------------------------
| Additional Department Dashboards
|--------------------------------------------------------------------------
*/

// Finance (FIN)
Route::prefix('dashboard/fin')->name('fin.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/manager', [DashboardController::class, 'index'])
        ->middleware(['role:FIN', 'position:manager'])
        ->name('manager.dashboard');

    Route::get('/staff', [DashboardController::class, 'index'])
        ->middleware(['role:FIN', 'position:staff'])
        ->name('employee.dashboard');
});

// Manufacturing (MAN)
Route::prefix('dashboard/man')->name('man.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/manager', [DashboardController::class, 'index'])
        ->middleware(['role:MAN', 'position:manager'])
        ->name('manager.dashboard');

    Route::get('/staff', [DashboardController::class, 'index'])
        ->middleware(['role:MAN', 'position:staff'])
        ->name('employee.dashboard');
});

// Inventory (INV)
Route::prefix('dashboard/inv')->name('inv.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/manager', [DashboardController::class, 'index'])
        ->middleware(['role:INV', 'position:manager'])
        ->name('manager.dashboard');

    Route::get('/staff', [DashboardController::class, 'index'])
        ->middleware(['role:INV', 'position:staff'])
        ->name('employee.dashboard');
});

// Order Management (ORD)
Route::prefix('dashboard/ord')->name('ord.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/manager', [DashboardController::class, 'index'])
        ->middleware(['role:ORD', 'position:manager'])
        ->name('manager.dashboard');

    Route::get('/staff', [DashboardController::class, 'index'])
        ->middleware(['role:ORD', 'position:staff'])
        ->name('employee.dashboard');
});

// Warehouse (WAR)
Route::prefix('dashboard/war')->name('war.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/manager', [DashboardController::class, 'index'])
        ->middleware(['role:WAR', 'position:manager'])
        ->name('manager.dashboard');

    Route::get('/staff', [DashboardController::class, 'index'])
        ->middleware(['role:WAR', 'position:staff'])
        ->name('employee.dashboard');
});

// CRM
Route::prefix('dashboard/crm')->name('crm.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/manager', [DashboardController::class, 'index'])
        ->middleware(['role:CRM', 'position:manager'])
        ->name('manager.dashboard');

    Route::get('/staff', [DashboardController::class, 'index'])
        ->middleware(['role:CRM', 'position:staff'])
        ->name('employee.dashboard');
});

// E-Commerce (ECO)
Route::prefix('dashboard/eco')->name('eco.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/manager', [DashboardController::class, 'index'])
        ->middleware(['role:ECO', 'position:manager'])
        ->name('manager.dashboard');

    Route::get('/manager/book', [BookController::class, 'book'])
        ->middleware(['role:ECO', 'position:manager'])
        ->name('manager.book');

    Route::get('/manager/credit', [CreditController::class, 'credit'])
        ->middleware(['role:ECO', 'position:manager'])
        ->name('manager.credit');

    Route::get('/staff', [DashboardController::class, 'index'])
        ->middleware(['role:ECO', 'position:staff'])
        ->name('employee.dashboard');
});

/*
|--------------------------------------------------------------------------
| Finance Module Routes
|--------------------------------------------------------------------------
*/
Route::prefix('finance')->name('finance.')->middleware(['auth', 'verified', 'position:staff,manager'])->group(function () {
    // Finance Dashboard
    Route::get('/dashboard', [FinanceDashboardController::class, 'index'])->name('dashboard');

    // Transactions Management
    Route::get('/transactions', [FinanceTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create', [FinanceTransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [FinanceTransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{transaction}/edit', [FinanceTransactionController::class, 'edit'])->name('transactions.edit');
    Route::patch('/transactions/{transaction}', [FinanceTransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transaction}', [FinanceTransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::get('/transactions/export', [FinanceTransactionController::class, 'export'])->name('transactions.export');

    // Invoices and Bills
    Route::get('/invoices-bills', [FinanceInvoiceBillController::class, 'index'])->name('invoices-bills.index');
    
    // Invoice routes
    Route::get('/invoices/create', [FinanceInvoiceBillController::class, 'createInvoice'])->name('invoices.create');
    Route::post('/invoices', [FinanceInvoiceBillController::class, 'storeInvoice'])->name('invoices.store');
    Route::patch('/invoices/{invoice}/pay', [FinanceInvoiceBillController::class, 'markInvoicePaid'])->name('invoices.pay');
    Route::get('/invoices/{invoice}/download', [FinanceInvoiceBillController::class, 'downloadInvoice'])->name('invoices.download');

    // Bill routes
    Route::get('/bills/create', [FinanceInvoiceBillController::class, 'createBill'])->name('bills.create');
    Route::post('/bills', [FinanceInvoiceBillController::class, 'storeBill'])->name('bills.store');
    Route::patch('/bills/{bill}/pay', [FinanceInvoiceBillController::class, 'markBillPaid'])->name('bills.pay');
    Route::get('/bills/{bill}/download', [FinanceInvoiceBillController::class, 'downloadBill'])->name('bills.download');

    // Reports
    Route::get('/reports', [FinanceReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [FinanceReportController::class, 'exportReport'])->name('reports.export');
});

require __DIR__.'/auth.php';
