<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslips</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="default-padding theme1">
    <div class="container-fluid">
        <!-- Search and Add Button Row -->
        <div class="row mt-2">
            <form action="" method="GET" class="col-auto d-flex justify-content-start align-items-center">
                <label for="searchInput" class="label me-2">Search:</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" name="query" style="width: 190px;">
                    <button class="btn btn-dark"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <div class="col d-flex justify-content-end ms-auto">
                <a href="{{ route('payslips.reports') }}" class="btn btn-info me-2">
                    <i class="bi bi-graph-up"></i> View Reports
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generatePayslipModal">
                    <i class="bi bi-plus-circle"></i> Generate Payroll
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!--- Payslip Table --->
        <div class="table-responsive mt-2">
            <table class="table table-striped table-hover table-bordered">
                <thead>
                    <tr>
                        <th style="width:80px">ID</th>
                        <th style="width:200px">Employee</th>
                        <th style="width:200px">Pay Period</th>
                        <th style="width:120px">Basic Pay</th>
                        <th style="width:120px">Overtime Pay</th>
                        <th style="width:120px">Deductions</th>
                        <th style="width:120px">Net Salary</th>
                        <th style="width:100px">Status</th>
                        <th style="width:200px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payslips as $payslip)
                        <tr>
                            <td>{{ $payslip->id }}</td>
                            <td>{{ $payslip->employee->name }}</td>
                            <td>{{ str_replace('_to_', ' to ', $payslip->pay_period) }}</td>
                            <td>₱{{ number_format($payslip->basic_pay, 2) }}</td>
                            <td>₱{{ number_format($payslip->overtime_pay, 2) }}</td>
                            <td>₱{{ number_format($payslip->total_deductions, 2) }}</td>
                            <td>₱{{ number_format($payslip->net_salary, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $payslip->payment_status === 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($payslip->payment_status) }}
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('payslips.show', $payslip->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="{{ route('payslips.pdf', $payslip->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-file-pdf"></i> PDF
                                </a>
                                @if($payslip->payment_status === 'pending')
                                    <form action="{{ route('payslips.mark-paid', $payslip->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="bi bi-check-circle"></i> Mark Paid
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Generate Payslip Modal -->
    <div class="modal fade" id="generatePayslipModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Payroll</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('payslips._generate_form')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 