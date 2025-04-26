<?php

namespace App\Http\Controllers;

use App\Models\Payroll1;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\CarbonPeriod;


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

    public function generate(Request $request)
    {
        $request->validate([
            'pay_frequency' => 'required|in:monthly,semi_monthly',
            'pay_period_choice' => 'required_if:pay_frequency,semi_monthly|in:first_half,second_half', // Only required for semi-monthly
        ]);
    
        $payFrequency = $request->pay_frequency;
    
        // For Monthly Payroll
        if ($payFrequency === 'monthly') {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
            $payPeriod = $start->toDateString() . '_to_' . $end->toDateString();
            $taxMethod  = 'calculateMonthlyTax';
            
        } 
        // For Semi-Monthly Payroll
        else {
            // Determine the selected period (first_half or second_half)
            if ($request->pay_period_choice === 'first_half') {
                $start = now()->startOfMonth();
                $end   = now()->day(15);
            } else {
                $start = now()->day(16);
                $end   = now()->endOfMonth();
            }
            $taxMethod = 'calculateSemiTax';
        }
    
        $payPeriod = $start->toDateString() . '_to_' . $end->toDateString();
    
    
        // Loop through employees and generate payroll
        foreach (Employee::all() as $employee) {
            $exists = Payroll1::where('employee_id', $employee->id)
                            ->where('pay_period', $payPeriod)
                            ->exists();
            if ($exists) continue;
    
            $monthlySalary = $employee->position->salary; // Monthly salary
            $semiMonthlySalary = $monthlySalary / 2; // Semi-monthly salary
    
            // Calculate working days (Mon–Fri) for the period
            $workingDays = collect(CarbonPeriod::create($start, $end))
                ->filter(function ($date) {
                    return $date->isWeekday(); // Mon-Fri only
                });
    
            $workingDaysCount = $workingDays->count();
    
            $dailyRate = $monthlySalary / 22;
    
            // Get the number of days the employee was present during the period
            $daysPresent = $employee->attendances()
                ->whereBetween('date', [$start, $end])
                ->where('status', 'Present')
                ->count();
    
            // Calculate absences and deductions
            $absentDays = $workingDaysCount - $daysPresent;
            $absenceDeduction = $absentDays * $dailyRate;

            if ($payFrequency === 'monthly') {
                $adjustedSalary = $monthlySalary - $absenceDeduction;
            } else {
                $adjustedSalary = $semiMonthlySalary - $absenceDeduction;
            }
// MATAY
$hourlyRate = $dailyRate / 8;
$weekdayOvertimeRate = $hourlyRate * 1.25;
$weekendOvertimeRate = $hourlyRate * 1.5;

$overtimeHours = 0;
$overtimePay = 0;

$attendances = $employee->attendances()
    ->whereBetween('date', [$start, $end])
    ->where('status', 'Present')
    ->whereNotNull('time_in')
    ->whereNotNull('time_out')
    ->select('date', 'time_in', 'time_out')
    ->get();

foreach ($attendances as $attendance) {
    if (!$attendance->time_out || !$attendance->time_in) continue;

    $attendanceDate = Carbon::parse($attendance->date, 'Asia/Singapore');
    $timeIn = Carbon::parse($attendance->time_in, 'Asia/Singapore');
    $timeOut = Carbon::parse($attendance->time_out, 'Asia/Singapore');

    if ($attendanceDate->isWeekend()) {
        $workedDurationMinutes = $timeIn->diffInMinutes($timeOut);

        if ($workedDurationMinutes >= 30) {
            $workedHours = $workedDurationMinutes / 60;
            $overtimeHours += $workedHours;
            $overtimePay += $workedHours * $weekendOvertimeRate;
        }
    } else {
        if ($timeOut->gt($timeOut->copy()->setTime(17, 0, 0))) {
            $fivePm = $timeOut->copy()->setTime(17, 0, 0);
            $overtimeMinutes = $fivePm->diffInMinutes($timeOut);

            if ($overtimeMinutes >= 30) {
                $workedHours = $overtimeMinutes / 60;
                $overtimeHours += $workedHours;
                $overtimePay += $workedHours * $weekdayOvertimeRate;
            }
        }
    }
}


            // Calculate total contributions
            $totalContributions = $employee->contributions->sum(function ($contribution) use ($adjustedSalary) {
                return $contribution->calculation_type === 'percent'
                    ? ($contribution->value / 100) * $adjustedSalary
                    : $contribution->value;
            });
    
            // Calculate taxable income and tax
            $taxableIncome = $adjustedSalary + $overtimePay - $totalContributions;
            $tax = $this->{$taxMethod}($taxableIncome);
            $netSalary = $taxableIncome - $tax;
    
            // Create the payroll record
            Payroll1::create([
                'employee_id' => $employee->id,
                'pay_period' => $payPeriod,
                'days_worked' => $daysPresent,
                'basic_pay' => $adjustedSalary,
                'overtime_pay' => $overtimePay,
                'total_deductions' => $totalContributions,
                'taxable_income' => $taxableIncome,
                'tax' => $tax,
                'net_salary' => $netSalary,
            ]);
        }
    
        return redirect()->route('payrolls.index')->with('success', 'Payroll generated for ' . $payPeriod . '!');
    }
    

    public function calculateSemiTax($taxable_income) {
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

    public function calculateMonthlyTax(float $taxable_income): float
    {
        if ($taxable_income <= 20_833) {
            // ₱20,833 and below
            return 0.0;
        } elseif ($taxable_income <= 33_332) {
            // ₱20,833 – ₱33,332: 15% of excess over ₱20,833
            return ($taxable_income - 20_833) * 0.15;
        } elseif ($taxable_income <= 66_666) {
            // ₱33,333 – ₱66,666: ₱1,875 + 20% of excess over ₱33,333
            return 1_875.00 + ($taxable_income - 33_333) * 0.20;
        } elseif ($taxable_income <= 166_666) {
            // ₱66,667 – ₱166,666: ₱8,541.80 + 25% of excess over ₱66,667
            return 8_541.80 + ($taxable_income - 66_667) * 0.25;
        } elseif ($taxable_income <= 666_666) {
            // ₱166,667 – ₱666,666: ₱33,541.80 + 30% of excess over ₱166,667
            return 33_541.80 + ($taxable_income - 166_667) * 0.30;
        } else {
            // ₱666,667 and above: ₱183,541.80 + 35% of excess over ₱666,667
            return 183_541.80 + ($taxable_income - 666_667) * 0.35;
        }
    }

}
