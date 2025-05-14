<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\ContributionTypeController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Dashboard & Features - Protected by auth middleware
Route::middleware('auth')->group(function () {
    // Redirect root to dashboard
    Route::get('/', function () {
        if (Auth::user()->role !== 'admin') {
            Auth::logout();
            return redirect('/login')->with('error', 'Please login as admin.');
        }
        return view('dashboard.index1');
    });

    // Admin dashboard
    Route::get('/dashboard', function () {
        if (Auth::user()->role !== 'admin') {
            Auth::logout();
            return redirect('/login')->with('error', 'Please login as admin.');
        }
        return view('dashboard.index1');
    })->name('dashboard');

    Route::get('/dashboard2', function () {
        if (Auth::user()->role !== 'admin') {
            Auth::logout();
            return redirect('/login')->with('error', 'Please login as admin.');
        }
        return view('dashboard.index2');
    })->name('dashboard2');

    // Admin-only routes - protected by role check in controllers
    Route::resource('positions', PositionController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('attendances', AttendanceController::class);
    Route::resource('shifts', ShiftController::class);
    Route::resource('contributions', ContributionController::class);
    Route::resource('contributiontypes', ContributionTypeController::class);
    Route::resource('loans', LoanController::class);

    // Payslip routes
    Route::get('/payslips/payrolls', [PayslipController::class, 'payrolls'])->name('payslips.payrolls');
    Route::get('/payslips/payrolls/{payPeriod}', [PayslipController::class, 'payrollDetails'])->name('payslips.payroll-details');
    Route::get('/payslips/reports', [PayslipController::class, 'reports'])->name('payslips.reports');
    Route::get('/payslips/reports/{payPeriod}', [PayslipController::class, 'reportDetails'])->name('payslips.report-details');
    Route::get('/payslips/{payslip}/pdf', [PayslipController::class, 'generatePDF'])->name('payslips.pdf');
    Route::patch('/payslips/{payslip}/mark-paid', [PayslipController::class, 'markAsPaid'])->name('payslips.mark-paid');
    Route::post('/payslips/generate', [PayslipController::class, 'generate'])->name('payslips.generate');
    Route::resource('payslips', PayslipController::class);

    // Additional admin routes
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::put('/employees/{employee}/update-role', [EmployeeController::class, 'updateRole'])->name('employees.updateRole');
    Route::post('/positions/store', [PositionController::class, 'store'])->name('position.store');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');


  Route::get('/empattendance', function () {
    return view('empattendance.index');
})->name('empattendance.index');

    
});