<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Shift;
use Carbon\CarbonPeriod;
use App\Models\Loan;
use App\Models\Contribution;
use App\Models\ContributionType;
use Illuminate\Support\Facades\Log;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $payrolls = Payroll::all();
    return view('payrolls.index1', compact('payrolls'));
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
    public function show(Payroll $payroll)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payroll $payroll)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payroll $payroll)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payroll $payroll)
    {
        try {
            $payroll->delete();
            return redirect()->route('payrolls.index')->with('success', 'Payroll record deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('payrolls.index')->with('error', 'Error deleting payroll record.');
        }
    }

    public function generate(Request $request)
    {
        // Set timezone to Manila
        date_default_timezone_set('Asia/Manila');
        
        $request->validate([
            'pay_frequency' => 'required|in:monthly,semi_monthly',
            'pay_month' => 'required',
            'pay_year' => 'required',
            'pay_period_choice' => 'required_if:pay_frequency,semi_monthly|in:first_half,second_half',
        ]);

        // Create the date from the month and year inputs
        $selectedDate = Carbon::create($request->pay_year, $request->pay_month, 1)->timezone('Asia/Manila');
        $payFrequency = $request->pay_frequency;

        // For Monthly Payroll
        if ($payFrequency === 'monthly') {
            $start = $selectedDate->copy()->startOfMonth();
            $end = $selectedDate->copy()->endOfMonth();
            $taxMethod = 'calculateMonthlyTax';
        } 
        // For Semi-Monthly Payroll
        else {
            if ($request->pay_period_choice === 'first_half') {
                $start = $selectedDate->copy()->startOfMonth();
                $end = $selectedDate->copy()->day(15);
            } else {
                $start = $selectedDate->copy()->day(16);
                $end = $selectedDate->copy()->endOfMonth();
            }
            $taxMethod = 'calculateSemiTax';
        }

        $payPeriod = $start->format('Y-m-d') . '_to_' . $end->format('Y-m-d');

        // Check if any payrolls exist for this period
        $existingPayrolls = Payroll::where('pay_period', $payPeriod)->exists();
        if ($existingPayrolls) {
            return redirect()->back()->with('error', 'Payroll records already exist for this period.');
        }

        // Debug information
        echo "<pre>Generating payroll for period: " . $payPeriod . "</pre>";

        // Loop through employees and generate payroll
        foreach (Employee::all() as $employee) {
            echo "<pre>Processing employee: " . $employee->id . "</pre>";
            
            $monthlySalary = $employee->position->salary;
            $semiMonthlySalary = $monthlySalary / 2;

            // Get employee shift
            $shift = $employee->shift;
            if (!$shift) {
                echo "<pre>Employee " . $employee->id . " has no shift assigned</pre>";
                continue; // Skip if no shift assigned
            }

            // Calculate standard working hours
            $workingDays = collect(CarbonPeriod::create($start, $end))
                ->filter(function ($date) {
                    return $date->isWeekday();
                });
            $workingDaysCount = $workingDays->count();

            echo "<pre>Working days in period: " . $workingDaysCount . "</pre>";

            // Calculate rates
            $standardWorkingDays = 22; // Standard monthly working days
            $shiftHours = 8; // Standard shift hours
            $hourlyRate = $monthlySalary / $standardWorkingDays / $shiftHours;

            echo "<pre>Monthly salary: " . $monthlySalary . "</pre>";
            echo "<pre>Hourly rate: " . $hourlyRate . "</pre>";

            $totalRegularHours = 0;
            $totalWeekdayOvertimeHours = 0;
            $totalWeekendHours = 0;

            // Process attendance records
            $attendanceRecords = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->where(function($query) {
                    $query->where('status', '!=', 'Absent')
                          ->orWhere('status', 'Leave');
                })
                ->get();

            echo "<pre>Found " . $attendanceRecords->count() . " attendance records</pre>";

            foreach ($attendanceRecords as $record) {
                echo "<pre>Processing attendance record for date: " . $record->date . "</pre>";
                echo "<pre>Time in: " . $record->time_in . ", Time out: " . $record->time_out . "</pre>";
                echo "<pre>Break out: " . $record->break_out . ", Break in: " . $record->break_in . "</pre>";
                
                // For leave days, count as full regular hours
                if ($record->status === 'Leave') {
                    $totalRegularHours += 8;
                    echo "<pre>Leave day - added 8 hours</pre>";
                    continue;
                }

                // Skip if no time records
                if (!$record->time_in || !$record->time_out) {
                    echo "<pre>Incomplete time records for date: " . $record->date . "</pre>";
                    continue;
                }

                $isWeekend = Carbon::parse($record->date)->isWeekend();
                
                // Get shift times for this day
                $shiftStart = Carbon::parse($record->date . ' ' . $shift->start_time)->timezone('Asia/Manila');
                $breakStart = Carbon::parse($record->date . ' ' . $shift->break_start_time)->timezone('Asia/Manila');
                $breakEnd = Carbon::parse($record->date . ' ' . $shift->break_end_time)->timezone('Asia/Manila');
                $shiftEnd = Carbon::parse($record->date . ' ' . $shift->end_time)->timezone('Asia/Manila');

                echo "<pre>Shift times - Start: " . $shiftStart->format('Y-m-d H:i:s') . 
                     ", Break start: " . $breakStart->format('Y-m-d H:i:s') . 
                     ", Break end: " . $breakEnd->format('Y-m-d H:i:s') . 
                     ", End: " . $shiftEnd->format('Y-m-d H:i:s') . "</pre>";

                // Handle overnight shifts
                if ($shiftEnd->lt($shiftStart)) {
                    $shiftEnd->addDay();
                }
                if ($breakEnd->lt($breakStart)) {
                    $breakEnd->addDay();
                }

                // Parse actual times worked
                $timeIn = Carbon::parse($record->time_in)->timezone('Asia/Manila');
                $timeOut = Carbon::parse($record->time_out)->timezone('Asia/Manila');
                $breakOut = $record->break_out ? Carbon::parse($record->break_out)->timezone('Asia/Manila') : null;
                $breakIn = $record->break_in ? Carbon::parse($record->break_in)->timezone('Asia/Manila') : null;

                // Handle overnight shifts for actual times
                if ($timeOut->lt($timeIn)) {
                    $timeOut->addDay();
                }
                if ($breakIn && $breakOut && $breakIn->lt($breakOut)) {
                    $breakIn->addDay();
                }

                $workedMinutes = 0;

                // Calculate first half of shift (from time in until break out)
                if ($timeIn && $breakOut) {
                    // Use actual time in, but not earlier than shift start
                    $startTime = $timeIn->lt($shiftStart) ? $shiftStart : $timeIn;
                    $firstHalfMinutes = $startTime->diffInMinutes($breakOut);
                    $workedMinutes += $firstHalfMinutes;
                    echo "<pre>First half minutes: " . $firstHalfMinutes . " (from " . $startTime->format('H:i:s') . " to " . $breakOut->format('H:i:s') . ")</pre>";
                }

                // Calculate second half of shift (from break in until time out)
                if ($breakIn && $timeOut) {
                    // Use actual time out, but not later than shift end
                    $endTime = $timeOut->gt($shiftEnd) ? $shiftEnd : $timeOut;
                    $secondHalfMinutes = $breakIn->diffInMinutes($endTime);
                    $workedMinutes += $secondHalfMinutes;
                    echo "<pre>Second half minutes: " . $secondHalfMinutes . " (from " . $breakIn->format('H:i:s') . " to " . $endTime->format('H:i:s') . ")</pre>";
                }

                $workedHours = $workedMinutes / 60;
                echo "<pre>Total worked hours for " . $record->date . ": " . $workedHours . "</pre>";

                if ($isWeekend) {
                    // For weekends, only count hours if more than 2 hours worked
                    if ($workedHours > 2) {
                        $totalWeekendHours += $workedHours;
                        echo "<pre>Added weekend hours: " . $workedHours . "</pre>";
                    }
                } else {
                    // Regular weekday
                    if ($workedHours > 8) {
                        $overtimeHours = $workedHours - 8;
                        // Only count overtime if more than 2 hours
                        if ($overtimeHours > 2) {
                            $totalWeekdayOvertimeHours += $overtimeHours;
                            echo "<pre>Added overtime hours: " . $overtimeHours . "</pre>";
                        }
                        $totalRegularHours += 8; // Cap regular hours at 8
                        echo "<pre>Added regular hours (capped): 8</pre>";
                    } else {
                        $totalRegularHours += $workedHours;
                        echo "<pre>Added regular hours: " . $workedHours . "</pre>";
                    }
                }
            }

            echo "<pre>Final hours - Regular: " . $totalRegularHours . 
                 ", Weekday OT: " . $totalWeekdayOvertimeHours . 
                 ", Weekend: " . $totalWeekendHours . "</pre>";

            // Calculate basic pay based on hours worked
            $basicPay = $totalRegularHours * $hourlyRate;
            if ($payFrequency === 'semi_monthly') {
                $basicPay = min($basicPay, $semiMonthlySalary); // Cap at semi-monthly salary
            } else {
                $basicPay = min($basicPay, $monthlySalary); // Cap at monthly salary
            }

            // Calculate overtime pay (1.25x for weekday, 1.5x for weekend)
            $overtimePay = ($totalWeekdayOvertimeHours * $hourlyRate * 1.25) + 
                          ($totalWeekendHours * $hourlyRate * 1.5);

            echo "<pre>Pay calculations - Basic: " . $basicPay . ", Overtime: " . $overtimePay . "</pre>";

            // Calculate all deductions
            $totalDeductions = 0;
            $contributionDeductions = 0;

            // Process contributions
            $contributions = $employee->contributions;
            foreach ($contributions as $contribution) {
                if ($contribution->calculation_type === 'fixed') {
                    $contributionDeductions += $contribution->value;
                } else { // percent
                    $contributionDeductions += ($basicPay * $contribution->value / 100);
                }
            }

            $totalDeductions = $contributionDeductions;
            echo "<pre>Total deductions: " . $totalDeductions . "</pre>";

            // Calculate taxable income and tax
            $taxableIncome = max(0, $basicPay + $overtimePay - $totalDeductions);
            $tax = $employee->$taxMethod($taxableIncome);

            echo "<pre>Final calculations - Taxable Income: " . $taxableIncome . ", Tax: " . $tax . "</pre>";

            // Create payroll record
            Payroll::create([
                'employee_id' => $employee->id,
                'pay_period' => $payPeriod,
                'hours_worked' => $totalRegularHours + $totalWeekdayOvertimeHours + $totalWeekendHours,
                'basic_pay' => max(0, $basicPay),
                'overtime_pay' => max(0, $overtimePay),
                'total_deductions' => max(0, $totalDeductions),
                'taxable_income' => $taxableIncome,
                'tax' => max(0, $tax),
                'net_salary' => max(0, $taxableIncome - $tax)
            ]);

            echo "<pre>Created payroll record for employee " . $employee->id . "</pre>";
        }

    
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
            return 0.0;
        } elseif ($taxable_income <= 33_332) {
            return ($taxable_income - 20_833) * 0.15;
        } elseif ($taxable_income <= 66_666) {
            return 1_875.00 + ($taxable_income - 33_333) * 0.20;
        } elseif ($taxable_income <= 166_666) {
            return 8_541.80 + ($taxable_income - 66_667) * 0.25;
        } elseif ($taxable_income <= 666_666) {
            return 33_541.80 + ($taxable_income - 166_667) * 0.30;
        } else {
            return 183_541.80 + ($taxable_income - 666_667) * 0.35;
        }
    }
}
