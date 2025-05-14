<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payrolls</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1rem;
        }
        .progress {
            height: 20px;
            border-radius: 10px;
        }
        .btn-group .btn {
            margin-right: 0.25rem;
        }
        .table th {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body class="default-padding theme1">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row align-items-center my-4">
            <div class="col">
                <h2 class="mb-0">
                    <i class="bi bi-cash-stack me-2"></i>
                    Payrolls Management
                </h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('payslips.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Payslips
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6 class="card-title">Total Payrolls</h6>
                        <h3 class="mb-0">{{ $payrolls->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payrolls Table -->
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">Payroll Records</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Pay Period</th>
                                <th>Type</th>
                                <th>Total Employees</th>
                                <th>Gross Pay</th>
                                <th>Deductions</th>
                                <th>Net Pay</th>
                                <th>Payment Status</th>
                                <th>Generated At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payrolls as $payroll)
                            <tr>
                                <td>{{ str_replace('_to_', ' to ', $payroll->pay_period) }}</td>
                                <td><span class="badge bg-info">{{ str_replace('_', '-', $payroll->period_type) }}</span></td>
                                <td>{{ $payroll->total_employees }}</td>
                                <td>₱{{ number_format($payroll->total_gross_pay, 2) }}</td>
                                <td>₱{{ number_format($payroll->total_deductions, 2) }}</td>
                                <td>₱{{ number_format($payroll->total_net_pay, 2) }}</td>
                                <td>
                                    <div class="progress">
                                        @php
                                            $percentage = ($payroll->paid_count / $payroll->total_count) * 100;
                                        @endphp
                                        <div class="progress-bar {{ $percentage == 100 ? 'bg-success' : 'bg-warning' }}" 
                                             role="progressbar" 
                                             style="width: {{ $percentage }}%"
                                             aria-valuenow="{{ $percentage }}"
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ $payroll->paid_count }}/{{ $payroll->total_count }}
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $payroll->generated_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('payslips.payroll-details', $payroll->pay_period) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> View Details
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