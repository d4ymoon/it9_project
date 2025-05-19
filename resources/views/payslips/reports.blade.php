<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shifts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body class="default-padding theme1" style="background-color: 	#f8f9fa">
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row align-items-center my-4">
        <div class="col">
            <h2 class="mb-0">
                <i class="bi bi-file-earmark-text me-2"></i>
                Payroll Reports
            </h2>
        </div>
       
    </div>

    <!-- Filter Section -->
    <div class="card mb-4" >
        <div class="card-body filter-form" >
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
            <div class="card text-white" style="background-color:#4a90e2">
                <div class="card-body">
                    <h6 class="card-title">Total Reports</h6>
                    <h3 class="mb-0">{{ $payrollReports->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white" >
                <div class="card-body">
                    <h6 class="card-title">Total Employees</h6>
                    <h3 class="mb-0">{{ $payrollReports->sum('total_employees') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-end">
    <h6 class="card-title">Total Net Pay</h6>
    <h3 class="mb-0">₱{{ number_format($payrollReports->sum('total_net_pay'), 2) }}</h3>
</div>
            </div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="card">
       <div class="card-header">
            <h5 class="card-title mb-0">Payroll Report Records</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover  table-striped table-bordered">
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
                                @php
                                    [$start, $end] = explode('_to_', $report->pay_period);
                                @endphp
                                <td>{{ \Carbon\Carbon::parse($start)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($end)->format('M d, Y') }}</td>
                                <td>{{ \Illuminate\Support\Str::title(str_replace('_', '-', $report->period_type)) }}</td>
                                <td>{{ $report->total_employees }}</td>
                                <td class="text-end">₱{{ number_format($report->total_gross_pay, 2) }}</td>
                                <td class="text-end">₱{{ number_format($report->total_deductions, 2) }}</td>
                                <td class="text-end">₱{{ number_format($report->total_net_pay, 2) }}</td>
                                <td>{{ $report->generated_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('payslips.report-details', $report->pay_period) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>