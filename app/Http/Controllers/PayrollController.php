<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\CarbonPeriod;


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
    echo "Pay Period: $payPeriod\n"; // Debugging output for pay period

    // Loop through employees and generate payroll
    foreach (Employee::all() as $employee) {
        echo "Processing Employee ID: {$employee->id}\n"; // Debugging output for employee

        $exists = Payroll::where('employee_id', $employee->id)
                        ->where('pay_period', $payPeriod)
                        ->exists();
        if ($exists) {
            echo "Payroll already exists for this employee in the period $payPeriod\n";
            continue; // Skip if payroll already exists
        }

        $monthlySalary = $employee->position->salary; // Monthly salary
        $semiMonthlySalary = $monthlySalary / 2; // Semi-monthly salary

        echo "Monthly Salary: $monthlySalary\n"; // Debugging output for salary
        echo "Semi-Monthly Salary: $semiMonthlySalary\n"; // Debugging output for semi-monthly salary

        // Calculate working days (Mon–Fri) for the period
        $workingDays = collect(CarbonPeriod::create($start, $end))
            ->filter(function ($date) {
                return $date->isWeekday(); // Mon-Fri only
            });

        $workingDaysCount = $workingDays->count();
        echo "Working Days: $workingDaysCount\n"; // Debugging output for working days count

        $dailyRate = $monthlySalary / 22; // Or use workingDaysCount
        $hourlyRate = $dailyRate / 8;
        echo "Daily Rate: $dailyRate\n"; // Debugging output for daily rate
        echo "Hourly Rate: $hourlyRate\n"; // Debugging output for hourly rate

        //------- CALCULATE ATTENDANCE HERE -------
        $totalRegularHours = 1; //TOTAL HOURS WORKED DURING SHIFT HOURS SHIFT HOURS IS BASED ON SHIFT TABLE
        $totalWeekdayOvertimeHours =  1; //TOTAL OVERTIME HOURS WORKED OVER SHIFT DURING WEEKDAYS, OVERTIME IS BASED ON SHIFT TABLE SHIFT END TIME
        $totalWeekendHours = 1; //TOTAL  OVERTIME WEEKEND HOURS WORKED DURING SHIFT;
        //------- CALCULATE ATTENDANCE HERE -------

        $totalRegularPay = $totalRegularHours * $hourlyRate;
        $totalWeekdayOvertimePay = $totalWeekdayOvertimeHours *  1.25;  // Weekday overtime rate
        $totalWeekendPay = $totalWeekendHours * 1.5; // Weekend overtime rate

        // Sum up all the payments
        echo "Total Regular Pay: $totalRegularPay\n"; // Debugging output for total regular pay
        echo "Total Weekday Overtime Pay: $totalWeekdayOvertimePay\n"; // Debugging output for total weekday overtime pay
        echo "Total Weekend Pay: $totalWeekendPay\n"; // Debugging output for total weekend pay

        $adjustedSalary = $totalRegularPay + $totalWeekdayOvertimePay + $totalWeekendPay;
        
        echo "Adjusted Salary: $adjustedSalary\n"; // Debugging output for adjusted salary

        // Calculate total contributions
        $totalContributions = $employee->contributions->sum(function ($contribution) use ($adjustedSalary) {
            return $contribution->calculation_type === 'percent'
                ? ($contribution->value / 100) * $adjustedSalary
                : $contribution->value;
        });

        echo "Total Contributions: $totalContributions\n"; // Debugging output for total contributions

        //------- CALCULATE LOAN DEDUCTIONS HERE -------
        //------- CALCULATE LOAN DEDUCTIONS HERE -------

        // Calculate taxable income and tax
        $overtimePay = ($totalWeekdayOvertimeHours * 1.25) + ($totalWeekendHours * 1.5);
        $taxableIncome = $adjustedSalary + $totalRegularPay + $totalWeekdayOvertimePay + $totalWeekendPay + $totalContributions;

        // Tax calculation based on employee's status
        $taxAmount = $employee->$taxMethod($taxableIncome); // Choose method based on pay frequency
        $netIncome = $taxableIncome - $taxAmount;

        echo "Taxable Income: $taxableIncome\n"; // Debugging output for taxable income
        echo "Tax Amount: $taxAmount\n"; // Debugging output for tax amount
        echo "Net Income: $netIncome\n"; // Debugging output for net income

        $payroll = Payroll::create([
            'employee_id' => $employee->id,
            'pay_period' => $payPeriod,
            'hours_worked', // TOTAL HOURS IN MONTH
            'basic_pay', // HOURLY RATE * TOTAL HOURS IN MONTH
            'overtime_pay' => $overtimePay, // OVERTIME HOURS * OVERTIME RATE
            'total_deductions' => $totalContributions, // You need to calculate this based on any deductions (e.g., absences, other deductions, or loans)
            'taxable_income' => $taxableIncome, // This is the income before tax
            'tax' => $taxAmount, // This is the calculated tax
            'net_salary' => $netIncome, // This is the final amount after deductions and tax
        ]);

        echo "Payroll record created for Employee ID: {$employee->id}\n"; // Debugging output for payroll record creation
    }

    echo "Payroll generation completed\n"; // Debugging output when payroll generation is complete
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
