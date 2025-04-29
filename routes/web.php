<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\ContributionTypeController;
use App\Http\Controllers\ShiftController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dash', function () {
    return view('dashboard.index');
});

Route::get('/dash1', function () {
    return view('dashboard.index2');
});



Route::get('/test', function () {
    return view('payrolls.index');
});

Route::resource('positions', PositionController::class);
Route::resource('employees', EmployeeController::class);

Route::resource('shifts', ShiftController::class);
Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
Route::resource('payrolls', PayrollController::class);

Route::resource('positions', PositionController::class);
Route::post('/positions/store', [PositionController::class, 'store'])->name('position.store');

 
Route::get('/payrolls/{payroll}/edit', [PayrollController::class, 'edit'])->name('payrolls.edit');


Route::get('/attendance', [AttendanceController::class, 'create'])->name('attendance.create');
Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
Route::get('/attendance1', [AttendanceController::class, 'index'])->name('attendance.index');


Route::post('/payrolls/generate', [PayrollController::class, 'generate'])->name('payrolls.generate');
Route::resource('contributions', ContributionController::class);
Route::resource('contributiontypes', ContributionTypeController::class);