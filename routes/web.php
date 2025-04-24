<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\DeductionTypeController;
use App\Http\Controllers\DeductionController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Payroll1Controller;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\ContributionTypeController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dash', function () {
    return view('dashboard.index');
});

Route::get('/test', function () {
    return view('payrolls1.index');
});

Route::resource('positions', PositionController::class);
Route::resource('employees', EmployeeController::class);
Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
Route::resource('payrolls', PayrollController::class);

Route::resource('positions', PositionController::class);
Route::post('/positions/store', [PositionController::class, 'store'])->name('position.store');

Route::put('/deductions/update-all', [DeductionController::class, 'updateAll'])->name('deductions.updateAll');
 
Route::get('/payrolls/{payroll}/edit', [PayrollController::class, 'edit'])->name('payrolls.edit');
Route::post('/deduction-types', [DeductionTypeController::class, 'store'])->name('deductiontypes.store');

Route::post('/deductions', [DeductionController::class, 'store'])->name('deductions.store');

Route::delete('/deductions/{deduction}', [DeductionController::class, 'destroy'])->name('deductions.destroy');
Route::put('/deductions/{id}', [DeductionController::class, 'update'])->name('deductions.update');

Route::get('/attendance', [AttendanceController::class, 'create'])->name('attendance.create');
Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
Route::get('/attendance1', [AttendanceController::class, 'index'])->name('attendance.index');


Route::post('/payrolls1/generate', [Payroll1Controller::class, 'generate'])->name('payrolls1.generate');
Route::resource('contributions', ContributionController::class);
Route::resource('contributiontypes', ContributionTypeController::class);