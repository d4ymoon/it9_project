<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\ContributionTypeController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Auth;

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
    });

    // Admin-only routes - protected by role check in controllers
    Route::resource('positions', PositionController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('attendances', AttendanceController::class);
    Route::resource('shifts', ShiftController::class);
    Route::resource('payrolls', PayrollController::class);
    Route::resource('contributions', ContributionController::class);
    Route::resource('contributiontypes', ContributionTypeController::class);
    Route::resource('loans', LoanController::class);

    // Additional admin routes
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::put('/employees/{employee}/update-role', [EmployeeController::class, 'updateRole'])->name('employees.updateRole');
    Route::post('/positions/store', [PositionController::class, 'store'])->name('position.store');
    Route::get('/payrolls/{payroll}/edit', [PayrollController::class, 'edit'])->name('payrolls.edit');
    Route::post('/payrolls/generate', [PayrollController::class, 'generate'])->name('payrolls.generate');
    
    // Attendance routes
    Route::get('/attendance', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance1', [AttendanceController::class, 'index'])->name('attendance.index');
});

Route::get('/test', function () {
    return view('payrolls.index');
});