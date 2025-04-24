<?php

namespace App\Http\Controllers;

use App\Models\Payroll1;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Attendance;

class Payroll1Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Payroll1 $payroll1)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payroll1 $payroll1)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payroll1 $payroll1)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payroll1 $payroll1)
    {
        //
    }

    public function generate()
{
    $start = now()->startOfMonth();
    $middle = now()->copy()->day(15);
    $end = now()->endOfMonth();

    // Determine if we're before or after 15th
    if (now()->day <= 15) {
        $periodStart = $start;
        $periodEnd = $middle;
    } else {
        $periodStart = $middle->addDay(); // 16th
        $periodEnd = $end;
    }

    $payPeriod = $periodStart->toDateString() . '_to_' . $periodEnd->toDateString();

    // Avoid duplication
    foreach (Employee::all() as $employee) {
        $exists = Payroll1::where('employee_id', $employee->id)
                         ->where('pay_period', $payPeriod)
                         ->exists();
        if ($exists) continue;

        $positionSalary = $employee->position->salary;

        $daysWorked = $employee->attendances()
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->where('status', 'Present')
            ->count();

        
        $overtimeHours = $employee->attendances()
        ->whereBetween('date', [$periodStart, $periodEnd])
        ->get()
        ->sum(function ($attendance) {
            if (!$attendance->time_out) {
                return 0; // No time_out = no overtime
            }

            $timeOut = Carbon::parse($attendance->time_out);
            $standardEnd = Carbon::createFromTime(17, 0, 0); // 5:00 PM

            // If logged out after 5 PM, calculate overtime
            if ($timeOut->gt($standardEnd)) {
                return $timeOut->diffInMinutes($standardEnd) / 60; // convert minutes to hours
            }

            return 0;
        });

        $overtimePay = $overtimeHours * 50; // Just an example rate

        $totalDeductions = $employee->calculateDeductions(); // Your own method

        $taxableIncome = $positionSalary + $overtimePay - $totalDeductions;
        $tax = $this->calculateTax($taxableIncome);
        $netSalary = $taxableIncome - $tax;

        Payroll1::create([
            'employee_id' => $employee->id,
            'pay_period' => $payPeriod,
            'days_worked' => $daysWorked,
            'basic_pay' => $positionSalary,
            'overtime_pay' => $overtimePay,
            'total_deductions' => $totalDeductions,
            'taxable_income' => $taxableIncome,
            'tax' => $tax,
            'net_salary' => $netSalary,
        ]);
    }

    return redirect()->route('payrolls.index')->with('success', 'Payroll generated!');
}

public function calculateTax($taxable_income) {
    if ($taxable_income <= 10417) {
        return 0;
    } elseif ($taxable_income <= 16666) {
        return 0 + 0.15 * ($taxable_income - 10417);
    } elseif ($taxable_income <= 33332) {
        return 1250 + 0.20 * ($taxable_income - 16667);
    } elseif ($taxable_income <= 83332) {
        return 5416.67 + 0.25 * ($taxable_income - 33333);
    } elseif ($taxable_income <= 333332) {
        return 20416.67 + 0.30 * ($taxable_income - 83333);
    } else {
        return 100416.67 + 0.35 * ($taxable_income - 333333);
    }
}


}
