<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payroll Report Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .container-fluid {
                width: 100%;
                padding: 0;
                margin: 0;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            .card-header {
                background-color: #f8f9fa !important;
                border-bottom: 2px solid #dee2e6 !important;
            }
            .table {
                border-collapse: collapse !important;
            }
            .table td, .table th {
                border: 1px solid #dee2e6 !important;
            }
            .table-striped tbody tr:nth-of-type(odd) {
                background-color: rgba(0,0,0,.05) !important;
            }
            .text-white {
                color: #000 !important;
            }
            .bg-success, .bg-info, [style*="background-color:#4a90e2"], [style*="background-color:#ff6b6b"] {
                background-color: #f8f9fa !important;
                border: 1px solid #dee2e6 !important;
            }
            .card .card-body {
                min-height: auto !important;
            }
        }
        .card .card-body {
            min-height: 140px;
        }
    </style>
</head>

<body class="default-padding theme1">
<div class="container-fluid">
    <!-- Header Row -->
    <div class="row mt-2 align-items-center justify-content-between no-print">
        <div class="col-auto">
            <a href="{{ route('payslips.reports') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back to Reports
            </a>
        </div>
        <div class="col text-center">
            <h2 class="mb-0">Payroll Report Details</h2>
            <p class="text-muted mb-0">
                Period: {{ $summary['period'] }} ({{ str_replace('_', '-', $summary['period_type']) }})
            </p>
        </div>
        <div class="col-auto d-flex justify-content-end">
            <button onclick="window.print()" class="btn btn-info btn-sm">
                <i class="bi bi-printer"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Print Header -->
    <div class="row mt-4 d-none d-print-block">
        <div class="col-12 text-center">
            <h2>Payroll Report Details</h2>
            <p class="mb-0">
                Period: {{ $summary['period'] }} ({{ str_replace('_', '-', $summary['period_type']) }})
            </p>
            <p class="mb-0">Generated: {{ $summary['generated_at']->format('F d, Y') }}</p>
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
                    <div class="card text-white" style="background-color:#4a90e2">
                        <div class="card-body">
                            <h6 class="card-title">Total Gross Pay</h6>
                            <h4 class="card-text">₱{{ number_format($summary['total_gross_pay'], 2) }}</h4>
                            <small>Basic: ₱{{ number_format($summary['total_basic_pay'], 2) }}</small><br>
                            <small>Overtime: ₱{{ number_format($summary['total_overtime_pay'], 2) }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white" style="background-color:#ff6b6b ">
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
                            <th class="no-print">Actions</th>
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
                                <td class="no-print">
                                    <a href="{{ route('payslips.show', $payslip->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
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
</body>
</html>