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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayslipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payslip::with('employee.position')->latest();

        // Filter by month
        if ($request->filled('month')) {
            $date = Carbon::parse($request->month . '-01');
            $query->where('pay_period', 'like', $date->format('Y-m') . '%');
        }

        // Search by employee name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        // Get min and max dates for the month filter
        $minDate = Payslip::min('pay_period');
        $maxDate = Payslip::max('pay_period');
        $minDate = $minDate ? Carbon::parse(explode('_', $minDate)[0])->format('Y-m') : now()->format('Y-m');
        $maxDate = $maxDate ? Carbon::parse(explode('_', $maxDate)[0])->format('Y-m') : now()->format('Y-m');

        $payslips = $query->paginate(10);
        return view('payslips.index1', compact('payslips', 'minDate', 'maxDate'));
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
        $employee = $payslip->employee;
        return view('payslips.show', compact('payslip', 'employee'));
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
            'period_type' => 'required|in:monthly,semi_monthly',
            'pay_month' => 'required|numeric|min:1|max:12',
            'pay_year' => 'required|numeric|min:2024|max:2030',
            'pay_period_choice' => 'required_if:period_type,semi_monthly|in:first_half,second_half'
        ]);

        // Format pay period string
        $month = str_pad($request->pay_month, 2, '0', STR_PAD_LEFT);
        $year = $request->pay_year;

        if ($request->period_type === 'monthly') {
            $startDate = "{$year}-{$month}-01";
            $endDate = date('Y-m-t', strtotime($startDate));
            $payPeriod = "{$startDate}_to_{$endDate}";
        } else {
            if ($request->pay_period_choice === 'first_half') {
                $payPeriod = "{$year}-{$month}-01_to_{$year}-{$month}-15";
            } else {
                $startDate = "{$year}-{$month}-16";
                $endDate = date('Y-m-t', strtotime("{$year}-{$month}-01"));
                $payPeriod = "{$startDate}_to_{$endDate}";
            }
        }

        // Get all active employees
        $employees = Employee::where('status', 'active')->get();

        // Use database transaction to prevent race conditions
        try {
            DB::beginTransaction();

            // Check if any payslips exist for this period
            $existingPayslips = Payslip::where('pay_period', $payPeriod)->exists();
            if ($existingPayslips) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Payslip records already exist for this period.');
            }

            foreach ($employees as $employee) {
            $monthlySalary = $employee->position->salary;
            $semiMonthlySalary = $monthlySalary / 2;

            // Get employee shift
            $shift = $employee->shift;
            if (!$shift) {
                continue; // Skip if no shift assigned
            }

            // Calculate standard working hours
            $standardWorkingDays = 22; // Standard monthly working days
            $shiftHours = 8; // Standard shift hours
            $hourlyRate = $monthlySalary / $standardWorkingDays / $shiftHours;

            $totalRegularHours = 0;
            $totalWeekdayOvertimeHours = 0;
            $totalWeekendHours = 0;

            // Process attendance records
            $attendanceRecords = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [
                    str_replace('_to_', '', explode('_', $payPeriod)[0]),
                    str_replace('_to_', '', explode('_', $payPeriod)[2])
                ])
                ->where(function($query) {
                    $query->where('status', 'Present')
                        ->orWhere('status', 'Leave');
                })
                ->get();

            foreach ($attendanceRecords as $record) {
                if ($record->status === 'Leave') {
                    // Count full 8 hours for leave
                    $totalRegularHours += 8;
                    continue;
                }

                $isWeekend = Carbon::parse($record->date)->isWeekend();

                // Use the correct column names from attendance table
                $recordRegularHours = $record->regular_hours ?? 0;
                $recordOvertimeHours = $record->overtime_hours ?? 0;
                $recordTotalHours = $record->total_hours ?? 0;

                if ($isWeekend) {
                    $totalWeekendHours += $recordTotalHours;
                } else {
                    $totalRegularHours += $recordRegularHours;
                    // Only count overtime if it exceeds 2 hours per day
                    if ($recordOvertimeHours > 2) {
                        $totalWeekdayOvertimeHours += $recordOvertimeHours;
                    }
                }
            }

            // Calculate basic pay based on regular hours
            $basicPay = $hourlyRate * $totalRegularHours;

            // Calculate overtime pay
            $overtimePay = 0;
            if ($totalWeekdayOvertimeHours > 0) {
                $overtimePay += $hourlyRate * 1.25 * $totalWeekdayOvertimeHours; // 25% premium for weekday overtime
            }
            if ($totalWeekendHours > 0) {
                $overtimePay += $hourlyRate * 1.30 * $totalWeekendHours; // 30% premium for weekend work
            }

            // Calculate total earnings
            $totalEarnings = $basicPay + $overtimePay;

            // Process deductions
            $totalDeductions = 0;
            $loanDeductions = 0;
            $contributionDeductions = 0;

            // Process loans
            $loanDeductionsData = $this->calculateLoanDeductions($employee, $basicPay);
            $loanDeductions = $loanDeductionsData['total'];
            $loanDetails = $loanDeductionsData['details'];

            // Process contributions
            foreach ($employee->contributions as $contribution) {
                if ($contribution->calculation_type === 'fixed') {
                    $contributionDeductions += $contribution->value;
                } else {
                    $contributionDeductions += ($basicPay * $contribution->value / 100);
                }
            }

            // Calculate total deductions (contributions + loans)
           $taxableIncome = max(0, $totalEarnings - $contributionDeductions - $loanDeductions);

            // Calculate tax based on taxable income
            $tax = $request->period_type === 'monthly' 
                ? $employee->calculateMonthlyTax($taxableIncome)
                : $employee->calculateSemiTax($taxableIncome);

            // Now calculate net salary
            $totalDeductions = $contributionDeductions + $loanDeductions + $tax;
           

            // Total deductions (to be saved in DB)
            

            $netSalary = $totalEarnings - $totalDeductions;

            // Create payslip
            Payslip::create([
                'employee_id' => $employee->id,
                'pay_period' => $payPeriod,
                'period_type' => $request->period_type,
                'hours_worked' => $totalRegularHours,
                'overtime_hours' => $totalWeekdayOvertimeHours + $totalWeekendHours,
                'basic_pay' => max(0, $basicPay),
                'overtime_pay' => max(0, $overtimePay),
                'loan_deductions' => max(0, $loanDeductions),
                'total_deductions' => max(0, $totalDeductions),
                'tax' => max(0, $tax),
                'net_salary' => $netSalary,
                'payment_status' => 'pending'
            ]);
        }

        DB::commit();
        return redirect()->route('payslips.index')->with('success', 'Payslips generated successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error generating payslips: ' . $e->getMessage());
        return redirect()->back()->with('error', 'An error occurred while generating payslips. Please try again.');
    }
}

public function generatePDF(Payslip $payslip)
{
    $employee = $payslip->employee;
    $pdf = PDF::loadView('payslips.pdf', compact('payslip', 'employee'));
    return $pdf->download('payslip_' . $employee->id . '_' . $payslip->pay_period . '.pdf');
}

public function markAsPaid(Payslip $payslip)
{
    $payslip->update(['payment_status' => 'paid']);
    return redirect()->back()->with('success', 'Payslip marked as paid successfully.');
}

public function markAllAsPaid(Request $request)
{
    $query = Payslip::where('payment_status', 'pending');
    
    // If month filter is applied
    if ($request->filled('month')) {
        $date = Carbon::parse($request->month . '-01');
        $query->where('pay_period', 'like', $date->format('Y-m') . '%');
    }
    
    $count = $query->update(['payment_status' => 'paid']);
    
    return redirect()->back()->with('success', $count . ' payslips marked as paid successfully.');
}

public function reports(Request $request)
{
    $query = Payslip::selectRaw('
        pay_period,
        period_type,
        COUNT(DISTINCT employee_id) as total_employees,
        SUM(basic_pay + overtime_pay) as total_gross_pay,
        SUM(total_deductions) as total_deductions,
        SUM(net_salary) as total_net_pay,
        MIN(created_at) as generated_at
    ');

    // Apply filters
    if ($request->filled('month') && $request->filled('year')) {
        $month = str_pad($request->month, 2, '0', STR_PAD_LEFT);
        $year = $request->year;
        $query->where('pay_period', 'like', "{$year}-{$month}%");
    } elseif ($request->filled('month')) {
        $month = str_pad($request->month, 2, '0', STR_PAD_LEFT);
        $query->where('pay_period', 'like', "%-{$month}%");
    } elseif ($request->filled('year')) {
        $query->where('pay_period', 'like', "{$request->year}%");
    }

    if ($request->filled('period_type')) {
        $query->where('period_type', $request->period_type);
    }

    $payrollReports = $query->groupBy('pay_period', 'period_type')
        ->orderBy('generated_at', 'desc')
        ->get()
        ->map(function($report) {
            $report->generated_at = Carbon::parse($report->generated_at);
            return $report;
        });

    return view('payslips.reports', compact('payrollReports'));
}

public function reportDetails($payPeriod)
{
    $payslips = Payslip::where('pay_period', $payPeriod)->with('employee')->get();
    
    // Calculate summary statistics
    $summary = [
        'period' => str_replace('_to_', ' to ', $payPeriod),
        'period_type' => $payslips->first()->period_type,
        'total_employees' => $payslips->count(),
        'total_gross_pay' => $payslips->sum(function($payslip) {
            return $payslip->basic_pay + $payslip->overtime_pay;
        }),
        'total_deductions' => $payslips->sum('total_deductions'),
        'total_net_pay' => $payslips->sum('net_salary'),
        'total_basic_pay' => $payslips->sum('basic_pay'),
        'total_overtime_pay' => $payslips->sum('overtime_pay'),
        'generated_at' => $payslips->first()->created_at
    ];

    return view('payslips.report-details', compact('payslips', 'summary'));
}

public function payrolls()
{
    // Group payslips by pay period and get summary data
    $payrolls = Payslip::selectRaw('
        pay_period,
        period_type,
        COUNT(DISTINCT employee_id) as total_employees,
        SUM(basic_pay + overtime_pay) as total_gross_pay,
        SUM(total_deductions) as total_deductions,
        SUM(net_salary) as total_net_pay,
        MIN(created_at) as generated_at,
        COUNT(CASE WHEN payment_status = "paid" THEN 1 END) as paid_count,
        COUNT(*) as total_count
    ')
    ->groupBy('pay_period', 'period_type')
    ->orderBy('generated_at', 'desc')
    ->get()
    ->map(function($payroll) {
        $payroll->generated_at = Carbon::parse($payroll->generated_at);
        return $payroll;
    });

    return view('payslips.payrolls', compact('payrolls'));
}

public function payrollDetails($payPeriod)
{
    $payslips = Payslip::where('pay_period', $payPeriod)->with('employee')->get();
    
    // Calculate summary statistics
    $summary = [
        'period' => str_replace('_to_', ' to ', $payPeriod),
        'period_type' => $payslips->first()->period_type,
        'total_employees' => $payslips->count(),
        'total_gross_pay' => $payslips->sum(function($payslip) {
            return $payslip->basic_pay + $payslip->overtime_pay;
        }),
        'total_deductions' => $payslips->sum('total_deductions'),
        'total_net_pay' => $payslips->sum('net_salary'),
        'total_basic_pay' => $payslips->sum('basic_pay'),
        'total_overtime_pay' => $payslips->sum('overtime_pay'),
        'paid_count' => $payslips->where('payment_status', 'paid')->count(),
        'pending_count' => $payslips->where('payment_status', 'pending')->count(),
        'generated_at' => $payslips->first()->created_at
    ];

    return view('payslips.payroll-details', compact('payslips', 'summary'));
}

public function employeePayslips()
{
    $employee = Auth::user()->employee;
    $payslips = Payslip::where('employee_id', $employee->id)
        ->orderBy('pay_period', 'desc')
        ->paginate(10);

    return view('employee.payslips.index', compact('payslips'));
}

private function calculateLoanDeductions($employee, $salary)
{
    $totalLoanDeductions = 0;
    $loanDetails = [];

    $activeLoans = $employee->loans()
        ->where('status', 'active')
        ->get();

    foreach ($activeLoans as $loan) {
        $monthlyInterest = $loan->calculateMonthlyInterest();
        $monthlyDeduction = $loan->calculateMonthlyDeduction($salary);
        
        if ($monthlyDeduction > 0) {
            $loanDetails[] = [
                'type' => $loan->loan_type,
                'amount' => $monthlyDeduction,
                'interest' => $monthlyInterest,
                'total' => $monthlyDeduction + $monthlyInterest
            ];
            
            $totalLoanDeductions += $monthlyDeduction + $monthlyInterest;
            
            // Process the loan payment
            $loan->processMonthlyPayment($salary);
        }
    }

    return [
        'total' => $totalLoanDeductions,
        'details' => $loanDetails
    ];
}
}
