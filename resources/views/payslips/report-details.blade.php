<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Report Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="default-padding theme1">
    <div class="container-fluid">
        <!-- Header Row -->
        <div class="row mt-2">
            <div class="col">
                <h2>Payroll Report Details</h2>
                <p class="text-muted">Period: {{ $summary['period'] }} ({{ str_replace('_', '-', $summary['period_type']) }})</p>
            </div>
            <div class="col-4 d-flex justify-content-end">
                <button onclick="window.print()" class="btn btn-info me-2">
                    <i class="bi bi-printer"></i> Print Report
                </button>
                <a href="{{ route('payslips.reports') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Reports
                </a>
            </div>
        </div>

        <!-- Financial Summary Card -->
        <div class="card mt-3">
            <div class="card-header">
                <h4>Financial Summary</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Gross Pay</h6>
                                <h4 class="card-text">₱{{ number_format($summary['total_gross_pay'], 2) }}</h4>
                                <small>Basic: ₱{{ number_format($summary['total_basic_pay'], 2) }}</small><br>
                                <small>Overtime: ₱{{ number_format($summary['total_overtime_pay'], 2) }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Deductions</h6>
                                <h4 class="card-text">₱{{ number_format($summary['total_deductions'], 2) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Net Pay</h6>
                                <h4 class="card-text">₱{{ number_format($summary['total_net_pay'], 2) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Employees</h6>
                                <h4 class="card-text">{{ $summary['total_employees'] }}</h4>
                                <small>Generated: {{ $summary['generated_at']->format('M d, Y') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payslips Table -->
        <div class="card mt-3">
            <div class="card-header">
                <h4>Individual Payslips</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Basic Pay</th>
                                <th>Overtime Pay</th>
                                <th>Gross Pay</th>
                                <th>Deductions</th>
                                <th>Net Pay</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payslips as $payslip)
                                <tr>
                                    <td>{{ $payslip->employee->name }}</td>
                                    <td>₱{{ number_format($payslip->basic_pay, 2) }}</td>
                                    <td>₱{{ number_format($payslip->overtime_pay, 2) }}</td>
                                    <td>₱{{ number_format($payslip->basic_pay + $payslip->overtime_pay, 2) }}</td>
                                    <td>₱{{ number_format($payslip->total_deductions, 2) }}</td>
                                    <td>₱{{ number_format($payslip->net_salary, 2) }}</td>
                                    <td>
                                        <a href="{{ route('payslips.show', $payslip->id) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <a href="{{ route('payslips.pdf', $payslip->id) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-file-pdf"></i> PDF
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        @media print {
            .btn { display: none !important; }
            .card { border: none !important; }
            .bg-primary, .bg-danger, .bg-success, .bg-info {
                background-color: transparent !important;
                color: black !important;
            }
        }
    </style>
</body>
</html> 