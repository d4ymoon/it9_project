@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row align-items-center my-4">
        <div class="col">
            <h2 class="mb-0">
                <i class="bi bi-file-earmark-text me-2"></i>
                Payroll Reports
            </h2>
        </div>
        <div class="col-auto">
            <a href="{{ route('payslips.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Payslips
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body filter-form">
            <form action="" method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        <option value="">All Months</option>
                        @foreach(range(1, 12) as $month)
                            <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select">
                        <option value="">All Years</option>
                        @foreach(range(date('Y'), date('Y')-5) as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Period Type</label>
                    <select name="period_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="monthly" {{ request('period_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="semi_monthly" {{ request('period_type') == 'semi_monthly' ? 'selected' : '' }}>Semi-Monthly</option>
                    </select>
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('payslips.reports') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Reports</h6>
                    <h3 class="mb-0">{{ $payrollReports->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Employees</h6>
                    <h3 class="mb-0">{{ $payrollReports->sum('total_employees') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Net Pay</h6>
                    <h3 class="mb-0">₱{{ number_format($payrollReports->sum('total_net_pay'), 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="card">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Payroll Report Records</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Pay Period</th>
                            <th>Type</th>
                            <th>Employees</th>
                            <th>Gross Pay</th>
                            <th>Deductions</th>
                            <th>Net Pay</th>
                            <th>Generated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payrollReports as $report)
                            <tr>
                                <td>{{ str_replace('_to_', ' to ', $report->pay_period) }}</td>
                                <td><span class="badge bg-info">{{ str_replace('_', '-', $report->period_type) }}</span></td>
                                <td>{{ $report->total_employees }}</td>
                                <td>₱{{ number_format($report->total_gross_pay, 2) }}</td>
                                <td>₱{{ number_format($report->total_deductions, 2) }}</td>
                                <td>₱{{ number_format($report->total_net_pay, 2) }}</td>
                                <td>{{ $report->generated_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('payslips.report-details', $report->pay_period) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 