<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="default-padding theme1">
    <div class="container-fluid">
        <!-- Header Row -->
        <div class="row mt-2">
            <div class="col">
                <h2>Payroll Details</h2>
                <p class="text-muted">Period: {{ $summary['period'] }} ({{ str_replace('_', '-', $summary['period_type']) }})</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('payslips.payrolls') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Payrolls
                </a>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Gross Pay</h5>
                        <h3 class="mb-0">₱{{ number_format($summary['total_gross_pay'], 2) }}</h3>
                        <small>Basic: ₱{{ number_format($summary['total_basic_pay'], 2) }}</small><br>
                        <small>Overtime: ₱{{ number_format($summary['total_overtime_pay'], 2) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Deductions</h5>
                        <h3 class="mb-0">₱{{ number_format($summary['total_deductions'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Net Pay</h5>
                        <h3 class="mb-0">₱{{ number_format($summary['total_net_pay'], 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="row mt-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Total Employees</h6>
                        <h4 class="mb-0">{{ $summary['total_employees'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Payment Status</h6>
                        <div class="progress" style="height: 25px;">
                            @php
                                $percentage = ($summary['paid_count'] / $summary['total_employees']) * 100;
                            @endphp
                            <div class="progress-bar {{ $percentage == 100 ? 'bg-success' : 'bg-warning' }}" 
                                 role="progressbar" 
                                 style="width: {{ $percentage }}%"
                                 aria-valuenow="{{ $percentage }}"
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $summary['paid_count'] }}/{{ $summary['total_employees'] }} Paid
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Average Net Pay</h6>
                        <h4 class="mb-0">₱{{ number_format($summary['total_net_pay'] / $summary['total_employees'], 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Generated On</h6>
                        <h4 class="mb-0">{{ $summary['generated_at']->format('M d, Y') }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payslips Table -->
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Individual Payslips</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Basic Pay</th>
                                <th>Overtime</th>
                                <th>Gross Pay</th>
                                <th>Deductions</th>
                                <th>Net Pay</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payslips as $payslip)
                            <tr>
                                <td>{{ $payslip->employee->full_name }}</td>
                                <td>₱{{ number_format($payslip->basic_pay, 2) }}</td>
                                <td>₱{{ number_format($payslip->overtime_pay, 2) }}</td>
                                <td>₱{{ number_format($payslip->basic_pay + $payslip->overtime_pay, 2) }}</td>
                                <td>₱{{ number_format($payslip->total_deductions, 2) }}</td>
                                <td>₱{{ number_format($payslip->net_salary, 2) }}</td>
                                <td>
                                    <span class="badge {{ $payslip->payment_status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($payslip->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('payslips.show', $payslip->id) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html> 