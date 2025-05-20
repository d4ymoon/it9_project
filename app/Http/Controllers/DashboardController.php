<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payslip;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get total active employees
        $totalEmployees = Employee::where('status', 'active')->count();

        // Get current month's payroll summary
        $currentMonth = Carbon::now()->format('Y-m');
        $currentMonthPayroll = Payslip::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth])
            ->selectRaw('
                COUNT(DISTINCT employee_id) as total_employees,
                SUM(basic_pay + overtime_pay) as total_gross_pay,
                SUM(total_deductions) as total_deductions,
                SUM(net_salary) as total_net_pay,
                COUNT(CASE WHEN payment_status = "paid" THEN 1 END) as paid_count,
                COUNT(*) as total_count
            ')
            ->first();

        // Get recent payrolls (last 5)
        $recentPayrolls = Payslip::selectRaw('
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
        ->limit(5)
        ->get()
        ->map(function($payroll) {
            $payroll->generated_at = Carbon::parse($payroll->generated_at);
            return $payroll;
        });

        // Get payroll statistics for the last 6 months
        $monthlyStats = Payslip::selectRaw('
            DATE_FORMAT(created_at, "%Y-%m") as month,
            SUM(net_salary) as total_net_pay,
            COUNT(DISTINCT employee_id) as total_employees
        ')
        ->whereRaw('created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)')
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get()
        ->map(function($stat) {
            $stat->month = Carbon::createFromFormat('Y-m', $stat->month)->format('M Y');
            return $stat;
        });

        return view('dashboard.index', compact(
            'totalEmployees',
            'currentMonthPayroll',
            'recentPayrolls',
            'monthlyStats'
        ));
    }

    public function index2()
    {
        // Get total active employees
        $totalEmployees = Employee::where('status', 'active')->count();

        // Get today's attendance count
        $todayAttendance = DB::table('attendances')
            ->whereDate('date', Carbon::today())
            ->count();

        // Get present employees count
        $presentEmployees = DB::table('attendances')
            ->whereDate('date', Carbon::today())
            ->where('status', 'Present')
            ->count();

        // Get yearly payroll total
        $yearlyPayroll = Payslip::whereYear('pay_period', Carbon::now()->year)
            ->sum('net_salary');

        // Get payroll statistics for the current year
        $monthlyStats = Payslip::selectRaw('
            DATE_FORMAT(pay_period, "%Y-%m") as month,
            SUM(net_salary) as total_net_pay,
            COUNT(DISTINCT employee_id) as total_employees
        ')
        ->whereYear('pay_period', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get()
        ->map(function($stat) {
            $stat->month = Carbon::createFromFormat('Y-m', $stat->month)->format('M Y');
            return $stat;
        });

        // Get overtime data for the current year
        $overtimeQuery = Payslip::selectRaw('
            DATE_FORMAT(pay_period, "%Y-%m") as month,
            SUM(overtime_hours) as total_overtime_hours
        ')
        ->whereYear('pay_period', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get();

        // Format overtime data for the chart
        $overtimeData = [
            'labels' => $overtimeQuery->map(function($item) {
                return Carbon::createFromFormat('Y-m', $item->month)->format('M Y');
            }),
            'data' => $overtimeQuery->pluck('total_overtime_hours')
        ];

        return view('dashboard.index2', compact(
            'totalEmployees',
            'todayAttendance',
            'presentEmployees',
            'yearlyPayroll',
            'overtimeData',
            'monthlyStats'
        ));
    }
} 