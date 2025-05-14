<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payslip;
use App\Models\Contribution;
use App\Models\ContributionType;
use App\Models\Loan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Shift;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;

class PayslipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payslips = Payslip::all();
        return view('payslips.index1', compact('payslips'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('payslips.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation logic here
    }

    /**
     * Display the specified resource.
     */
    public function show(Payslip $payslip)
    {
        return view('payslips.show', compact('payslip'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payslip $payslip)
    {
        return view('payslips.edit', compact('payslip'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payslip $payslip)
    {
        // Update logic here
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payslip $payslip)
    {
        try {
            $payslip->delete();
            return redirect()->route('payslips.index')->with('success', 'Payslip record deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('payslips.index')->with('error', 'Error deleting payslip record.');
        }
    }

    public function generate(Request $request)
{
        // Validate request
    $request->validate([
            'pay_period' => 'required|string',
            'period_type' => 'required|in:monthly,semi-monthly'
    ]);

        $payPeriod = $request->pay_period;
        $periodType = $request->period_type;

        // Get all active employees
        $employees = Employee::where('status', 'active')->get();

        if ($periodType === 'monthly') {
            // For Monthly Payslip
            $workingDays = 22; // Assuming 22 working days per month
            $workingHours = $workingDays * 8; // 8 hours per day
        } else {
            // For Semi-Monthly Payslip
            $workingDays = 11; // Half of monthly working days
            $workingHours = $workingDays * 8;
        }

        // Check if any payslips exist for this period
        $existingPayslips = Payslip::where('pay_period', $payPeriod)->exists();
        if ($existingPayslips) {
            return redirect()->back()->with('error', 'Payslip records already exist for this period.');
        }

        // Start generating payslips
        echo "<pre>Generating payslip for period: " . $payPeriod . "</pre>";

        // Loop through employees and generate payslip
        foreach ($employees as $employee) {
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
            $workingDays = collect(CarbonPeriod::create($payPeriod))
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
                ->whereBetween('date', [$payPeriod])
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
            if ($periodType === 'semi-monthly') {
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

            // Calculate loan deductions
            $loanDeductions = 0;
            $activeLoans = Loan::where('employee_id', $employee->id)
                ->where('status', 'active')
                ->where('remaining_balance', '>', 0)
                ->get();

            foreach ($activeLoans as $loan) {
                // Calculate loan deduction based on percentage of basic pay
                $loanDeduction = min(
                    $loan->remaining_balance,
                    ($basicPay * $loan->deduction_percentage / 100)
                );
                
                $loanDeductions += $loanDeduction;

                // Update remaining balance
                $loan->remaining_balance = max(0, $loan->remaining_balance - $loanDeduction);
                if ($loan->remaining_balance == 0) {
                    $loan->status = 'paid';
                }
                $loan->save();
        
                echo "<pre>Processed loan deduction: " . $loanDeduction . " for loan ID: " . $loan->id . "</pre>";
            }

            // Process contributions
            $contributions = $employee->contributions;
            foreach ($contributions as $contribution) {
                if ($contribution->calculation_type === 'fixed') {
                    $contributionDeductions += $contribution->value;
                } else { // percent
                    $contributionDeductions += ($basicPay * $contribution->value / 100);
                }
            }

            $totalDeductions = $contributionDeductions + $loanDeductions;
            echo "<pre>Total deductions: " . $totalDeductions . " (Contributions: " . $contributionDeductions . ", Loans: " . $loanDeductions . ")</pre>";

        // Calculate taxable income and tax
            $taxableIncome = max(0, $basicPay + $overtimePay - $totalDeductions);
            $tax = $employee->calculateTax($taxableIncome);

            echo "<pre>Final calculations - Taxable Income: " . $taxableIncome . ", Tax: " . $tax . "</pre>";

            // Create payslip record
            Payslip::create([
            'employee_id' => $employee->id,
            'pay_period' => $payPeriod,
                'period_type' => $periodType,
                'basic_pay' => max(0, $basicPay),
                'overtime_pay' => max(0, $overtimePay),
            'hours_worked' => $totalRegularHours + $totalWeekdayOvertimeHours + $totalWeekendHours,
                'overtime_hours' => $overtimeHours,
                'tax' => max(0, $tax),
                'total_deductions' => max(0, $totalDeductions),
                'loan_deductions' => max(0, $loanDeductions),
                'net_salary' => max(0, $taxableIncome - $tax),
                'payment_status' => 'pending'
        ]);

            echo "<pre>Created payslip record for employee " . $employee->id . "</pre>";
        }

        return redirect()->route('payslips.index')->with('success', 'Payslips generated successfully.');
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
