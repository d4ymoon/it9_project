<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Statistics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard Statistics</h1>
    <div class="row mt-4">
        <!-- Total Employees Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Employees</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Employee::where('status', 'active')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fs-2 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Attendance Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Today's Attendance</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Attendance::whereDate('date', today())->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-check fs-2 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Present Employees Today Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Present Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Attendance::whereDate('date', today())->where('status', 'Present')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-check fs-2 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Monthly Payroll Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Monthly Payroll (Net)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₱{{ number_format(\App\Models\Payslip::whereMonth('pay_period', now()->month)
                                    ->whereYear('pay_period', now()->year)
                                    ->sum('net_salary'), 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cash-stack fs-2 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Chart -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Weekly Attendance Overview</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Present</th>
                                    <th>Late</th>
                                    <th>Absent</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $dates = collect(range(6, 0))->map(function($days) {
                                        return now()->subDays($days)->format('Y-m-d');
                                    });
                                @endphp

                                @foreach($dates as $date)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</td>
                                        <td>{{ \App\Models\Attendance::whereDate('date', $date)->where('status', 'Present')->count() }}</td>
                                        <td>{{ \App\Models\Attendance::whereDate('date', $date)->where('status', 'Late')->count() }}</td>
                                        <td>{{ \App\Models\Employee::where('status', 'active')->count() - 
                                             \App\Models\Attendance::whereDate('date', $date)->count() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Position Distribution -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Employees by Position</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Position</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\Position::withCount('employees')->get() as $position)
                                    <tr>
                                        <td>{{ $position->name }}</td>
                                        <td>{{ $position->employees_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 4px solid #4e73df;
}
.border-left-success {
    border-left: 4px solid #1cc88a;
}
.border-left-info {
    border-left: 4px solid #36b9cc;
}
.border-left-warning {
    border-left: 4px solid #f6c23e;
}
.text-xs {
    font-size: .7rem;
}
.card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
}
</style>
