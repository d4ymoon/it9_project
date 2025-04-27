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
            $hourlyRate = $dailyRate / 8;
    
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
$totalRegularHours = 0;
$totalWeekdayOvertimeHours = 0;
$totalWeekendHours = 0;
$weekdayOvertimeRate = 1.25 * $hourlyRate; // 25% extra for weekdays overtime
$weekendOvertimeRate = 1.5 * $hourlyRate;  // 50% extra for weekends

$attendances = $employee->attendances()
    ->whereBetween('date', [$start, $end])
    ->where('status', 'Present')
    ->whereNotNull('time_in')
    ->whereNotNull('time_out')
    ->select('date', 'time_in', 'time_out')
    ->get();

echo "Attendance Records Retrieved: " . $attendances->count() . "<br>";

foreach ($attendances as $attendance) {
    $attendanceDate = Carbon::parse($attendance->date, 'Asia/Singapore');
    $timeIn = Carbon::parse($attendance->time_in, 'Asia/Singapore');
    $timeOut = Carbon::parse($attendance->time_out, 'Asia/Singapore');

    // Ensure that Time In and Time Out are on the correct day
    $timeIn->setDate($attendanceDate->year, $attendanceDate->month, $attendanceDate->day);
    $timeOut->setDate($attendanceDate->year, $attendanceDate->month, $attendanceDate->day);

    echo "<br>Processing Attendance on: " . $attendanceDate->toDateString() . "<br>";
    echo "Time In: " . $timeIn->toDateTimeString() . " | Time Out: " . $timeOut->toDateTimeString() . "<br>";

    if (!$timeIn || !$timeOut || $timeOut->lessThanOrEqualTo($timeIn)) {
        echo "Skipping Invalid Attendance (Time In: $timeIn, Time Out: $timeOut)<br>";
        continue; // Skip invalid entries
    }

    $workedMinutes = $timeIn->diffInMinutes($timeOut);
    $workedHours = $workedMinutes / 60;
    echo "Total Worked Time: " . number_format($workedHours, 2) . " hours<br>";

    // Handle Weekend Work
    if ($attendanceDate->isWeekend()) {
        if ($workedHours > 2) {
            $totalWeekendHours += $workedHours;
            echo "Weekend Work (More than 2 hours): " . number_format($workedHours, 2) . " hours<br>";
        } else {
            echo "Weekend Work (Less than 2 hours): No Weekend Hours added<br>";
        }
    } else {
        // Handle Weekday Work (Monday-Friday)
        $startOfWork = $attendanceDate->copy()->setTime(8, 0, 0);   // 8:00 AM
        $endOfWork = $attendanceDate->copy()->setTime(17, 0, 0);    // 5:00 PM

        echo "Regular Work Start: " . $startOfWork->toDateTimeString() . " | Regular Work End: " . $endOfWork->toDateTimeString() . "<br>";

        // Regular Work Time Calculation
        // 1. Set regular time in to 8:00 AM of the attendance date
        $regularTimeIn = $timeIn->greaterThan($startOfWork) ? $timeIn : $startOfWork;
        
        // 2. Set regular time out to 5:00 PM if the clock-out time is later
        $regularTimeOut = $timeOut->lessThanOrEqualTo($endOfWork) ? $timeOut : $endOfWork;

        // Ensure regular work hours are within the specified work period (8 AM to 5 PM)
        if ($regularTimeIn->lessThan($regularTimeOut)) {
            $regularMinutes = $regularTimeIn->diffInMinutes($regularTimeOut);
            $regularHours = $regularMinutes / 60;
            $totalRegularHours += $regularHours;
            echo "Regular Working Hours: " . number_format($regularHours, 2) . " hours<br>";
        } else {
            echo "No Regular Working Hours (Time In: $regularTimeIn, Time Out: $regularTimeOut)<br>";
        }

        // Overtime Calculation (after 5:00 PM)
        if ($timeOut->greaterThan($endOfWork)) {
            // Ensure we're comparing the same date for overtime calculation
            $overtimeStart = $endOfWork; // 5:00 PM of the attendance date
            $overtimeEnd = $attendanceDate->copy()->setTime($timeOut->hour, $timeOut->minute, $timeOut->second);
            
            // Calculate overtime duration
            $overtimeMinutes = $overtimeStart->diffInMinutes($overtimeEnd);
            $overtimeHours = $overtimeMinutes / 60;

            echo "Overtime Time Calculation: End of Work (5:00 PM) to Time Out (" . $overtimeEnd->toDateTimeString() . ") = $overtimeHours hours<br>";

            // Ensure that overtime is only counted if it's more than 2 hours
            if ($overtimeHours > 2) {
                $totalWeekdayOvertimeHours += $overtimeHours;
                echo "Weekday Overtime (More than 2 hours): " . number_format($overtimeHours, 2) . " hours<br>";
            } else {
                echo "No Overtime (under 2 hours)<br>";
            }
        } else {
            echo "No Overtime Worked (Time Out before 5:00 PM)<br>";
        }
    }
}

$totalRegularPay = $totalRegularHours * $hourlyRate;
$totalWeekdayOvertimePay = $totalWeekdayOvertimeHours * $weekdayOvertimeRate;
$totalWeekendPay = $totalWeekendHours * $weekendOvertimeRate;

// Sum up all the payments
$adjustedSalary = $totalRegularPay + $totalWeekdayOvertimePay + $totalWeekendPay;

echo "Regular Pay: " . number_format($totalRegularPay, 2) . " (Total Regular Hours: $totalRegularHours * Hourly Rate: $hourlyRate)<br>";
echo "Weekday Overtime Pay: " . number_format($totalWeekdayOvertimePay, 2) . " (Total Weekday Overtime Hours: $totalWeekdayOvertimeHours * Weekday Overtime Rate: $weekdayOvertimeRate)<br>";
echo "Weekend Pay: " . number_format($totalWeekendPay, 2) . " (Total Weekend Hours: $totalWeekendHours * Weekend Overtime Rate: $weekendOvertimeRate)<br>";


echo "<br>Results:<br>";
echo "Total Regular Working Hours (Mon-Fri 8am-5pm): " . number_format($totalRegularHours, 2) . " hours<br>";
echo "Total Weekday Overtime Hours (Mon-Fri after 5pm, >2h): " . number_format($totalWeekdayOvertimeHours, 2) . " hours<br>";
echo "Total Weekend Working Hours (Sat/Sun, >2h): " . number_format($totalWeekendHours, 2) . " hours<br>";


exit();
            // Calculate total contributions
            $totalContributions = $employee->contributions->sum(function ($contribution) use ($adjustedSalary) {
                return $contribution->calculation_type === 'percent'
                    ? ($contribution->value / 100) * $adjustedSalary
                    : $contribution->value;
            });
    
            // Calculate taxable income and tax
            $overtimePay = ($totalWeekdayOvertimeHours * $weekdayOvertimeRate) + ($totalWeekendHours * $weekendOvertimeRate);
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
