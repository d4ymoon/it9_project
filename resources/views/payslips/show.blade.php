<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $employee->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>Payslip</h2>
                    <div class="no-print">
                        <a href="{{ route('payslips.index') }}" class="btn btn-secondary">Back</a>
                        <a href="{{ route('payslips.pdf', $payslip->id) }}" class="btn btn-primary">Download PDF</a>
                        <button onclick="window.print()" class="btn btn-info">
                            <i class="bi bi-printer"></i> Print
                        </button>
                        @if($payslip->payment_status === 'pending')
                            <form action="{{ route('payslips.mark-paid', $payslip->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success">Mark as Paid</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Company Header -->
                <div class="text-center mb-4">
                    <h3>Company Name</h3>
                    <p>123 Company Street, City, Country</p>
                </div>

                <!-- Payment Status Badge -->
                <div class="text-end mb-3">
                    <span class="badge bg-{{ $payslip->payment_status === 'paid' ? 'success' : 'warning' }}">
                        {{ ucfirst($payslip->payment_status) }}
                    </span>
                </div>

                <!-- Employee Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Employee Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td>Name:</td>
                                <td>{{ $employee->name }}</td>
                            </tr>
                            <tr>
                                <td>Position:</td>
                                <td>{{ $employee->position->name }}</td>
                            </tr>
                            <tr>
                                <td>Employee ID:</td>
                                <td>{{ $employee->id }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Pay Period Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td>Pay Period:</td>
                                <td>{{ str_replace('_to_', ' to ', $payslip->pay_period) }}</td>
                            </tr>
                            <tr>
                                <td>Hours Worked:</td>
                                <td>{{ $payslip->hours_worked }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Earnings -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Earnings</h5>
                        <table class="table">
                            <tr>
                                <td>Basic Pay</td>
                                <td class="text-end">₱{{ number_format($payslip->basic_pay, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Overtime Pay</td>
                                <td class="text-end">₱{{ number_format($payslip->overtime_pay, 2) }}</td>
                            </tr>
                            <tr class="table-secondary">
                                <td><strong>Total Earnings</strong></td>
                                <td class="text-end"><strong>₱{{ number_format($payslip->basic_pay + $payslip->overtime_pay, 2) }}</strong></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Deductions</h5>
                        <table class="table">
                            <tr>
                                <td>Contributions</td>
                                <td class="text-end">₱{{ number_format($payslip->total_deductions - $payslip->loan_deductions, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Loan Deductions</td>
                                <td class="text-end">₱{{ number_format($payslip->loan_deductions, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Tax</td>
                                <td class="text-end">₱{{ number_format($payslip->tax, 2) }}</td>
                            </tr>
                            <tr class="table-secondary">
                                <td><strong>Total Deductions</strong></td>
                                <td class="text-end"><strong>₱{{ number_format($payslip->total_deductions + $payslip->tax, 2) }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Net Pay -->
                <div class="row mb-4">
                    <div class="col-12">
                        <table class="table table-bordered">
                            <tr class="table-primary">
                                <td><h5 class="mb-0">Net Pay</h5></td>
                                <td class="text-end"><h5 class="mb-0">₱{{ number_format($payslip->net_salary, 2) }}</h5></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5>Payment Details</h5>
                        <table class="table table-bordered">
                            <tr>
                                <td>Payment Method:</td>
                                <td>
                                    @if($employee->payment_method === 'bank')
                                        Bank Transfer<br>
                                        Bank: {{ $employee->bank_name }}<br>
                                        Account Number: {{ substr($employee->bank_acct, 0, -4) }}****
                                    @else
                                        Cash
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Signatures -->
                <div class="row mt-5">
                    <div class="col-md-6">
                        <div class="border-top border-dark" style="width: 200px;">
                            <p class="mt-2">Prepared by</p>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="border-top border-dark ms-auto" style="width: 200px;">
                            <p class="mt-2">Received by</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 