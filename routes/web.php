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
use App\Http\Controllers\EmployeeDashboardController;

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Redirect root to appropriate dashboard based on role
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }
    return Auth::user()->role === 'admin' 
        ? redirect('/admin/dashboard')
        : redirect('/employee/dashboard');
});

// Admin Routes
Route::middleware(['web', 'auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index1');
    })->name('admin.dashboard');

    Route::get('/dashboard2', function () {
        return view('dashboard.index2');
    })->name('dashboard2');

    // Payslip routes
    Route::get('payslips/payrolls', [PayslipController::class, 'payrolls'])->name('payslips.payrolls');
    Route::get('payslips/payrolls/{payPeriod}', [PayslipController::class, 'payrollDetails'])->name('payslips.payroll-details');
    Route::get('payslips/reports', [PayslipController::class, 'reports'])->name('payslips.reports');
    Route::get('payslips/reports/{payPeriod}', [PayslipController::class, 'reportDetails'])->name('payslips.report-details');
    Route::get('payslips/{payslip}/pdf', [PayslipController::class, 'generatePDF'])->name('payslips.pdf');
    Route::patch('payslips/{payslip}/mark-paid', [PayslipController::class, 'markAsPaid'])->name('payslips.mark-paid');
    Route::post('payslips/generate', [PayslipController::class, 'generate'])->name('payslips.generate');

    // Add the mark-all-paid route before resource routes
    Route::patch('payslips/mark-all', [PayslipController::class, 'markAllAsPaid'])->name('payslips.mark-all-paid');

    // Resource routes
    Route::resource('positions', PositionController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('attendances', AttendanceController::class);
    Route::resource('shifts', ShiftController::class);
    Route::resource('contributions', ContributionController::class);
    Route::resource('contributiontypes', ContributionTypeController::class);
    Route::resource('loans', LoanController::class);
    Route::resource('payslips', PayslipController::class);

    // Additional admin routes
    Route::put('employees/{employee}/update-role', [EmployeeController::class, 'updateRole'])->name('employees.updateRole');
    Route::post('positions/store', [PositionController::class, 'store'])->name('position.store');
});

// Employee Routes
Route::middleware(['web', 'auth', \App\Http\Middleware\EmployeeMiddleware::class])->prefix('employee')->group(function () {
    Route::get('/dashboard', function () {
        return view('employee.index');
    })->name('employee.dashboard');
    
    // Employee attendance routes
    Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('employee.attendance.create');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('employee.attendance.store');
    Route::get('/attendance', [AttendanceController::class, 'employeeAttendance'])->name('employee.attendance.index');
    
    // Employee loan view route
    Route::get('/loans', [LoanController::class, 'employeeLoans'])->name('employee.loans.index');
    
    // Employee payslip view route
    Route::get('/payslips/{payslip}/pdf', [PayslipController::class, 'generatePDF'])->name('payslips.download');
    Route::get('/payslips/{payslip}', [PayslipController::class, 'show'])->name('employee.payslips.show');
    Route::get('/payslips', [PayslipController::class, 'employeePayslips'])->name('employee.payslips.index');

    // Password change routes
    Route::get('/change-password', [EmployeeDashboardController::class, 'showChangePasswordForm'])->name('employee.change-password.form');
    Route::post('/change-password', [EmployeeDashboardController::class, 'changePassword'])->name('employee.change-password');

    // Employee contributions route
    Route::get('/contributions', [EmployeeDashboardController::class, 'contributions'])->name('employee.contributions');
});

// Profile routes - accessible by both admin and employee
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Public attendance route
Route::get('/empattendance', function () {
    return view('empattendance.index');
})->name('empattendance.index');