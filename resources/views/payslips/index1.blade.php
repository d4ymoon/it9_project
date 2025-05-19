<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslips</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="default-padding theme1" style="background-color: 	#f8f9fa">
    <div class="container-fluid">
        <!-- Search and Filter Row -->
        <div class="row mt-2 align-items-end">
            <div class="col-auto">
                <form action="{{ route('payslips.index') }}" method="GET" class="row g-3 align-items-end">
                    <!-- Month Filter -->
                    <div class="col-auto">
                        <input type="month" class="form-control" id="month" name="month" placeholder="Month..." 
                               value="{{ request('month') }}"
                               min="{{ $minDate }}" max="{{ $maxDate }}">
                    </div>

                    <!-- Search -->
                    <div class="col-auto">
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Employee name...">
                            <button class="btn btn-dark" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-auto">
                        <a href="{{ route('payslips.index') }}" class="btn btn-secondary">Reset Filters</a>
                    </div>
                </form>
            </div>

            <!-- Action Buttons -->
            <div class="col d-flex justify-content-end ms-auto">
                <a href="{{ route('payslips.reports') }}" class="btn btn-info me-2">
                    <i class="bi bi-graph-up"></i> View Reports
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generatePayslipModal">
                    <i class="bi bi-plus-circle"></i> Generate Payroll
                </button>
                @if($payslips->where('payment_status', 'pending')->count() > 0)
                    <form action="{{ route('payslips.mark-all-paid') }}" method="POST" class="d-inline ms-2" id="markAllPaidForm">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="month" value="{{ request('month') }}">
                        <button type="button" class="btn btn-success" onclick="confirmMarkAllPaid()">
                            <i class="bi bi-check-all"></i> Mark All as Paid
                        </button>
                    </form>
                @endif
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
                        <th style="width:">Employee</th>
                        <th style="width:">Pay Period</th>
                        <th style="width:">Basic Pay</th>
                        <th style="width:">Overtime Pay</th>
                        <th style="width:">Deductions</th>
                        <th style="width:">Net Salary</th>
                        <th style="width:">Status</th>
                        <th style="width:">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payslips as $payslip)
                        <tr>
                            <td>{{ $payslip->employee->name }}</td>
                            @php
                                [$start, $end] = explode('_to_', $payslip->pay_period);
                            @endphp
                            <td>{{ \Carbon\Carbon::parse($start)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($end)->format('M d, Y') }}</td>
                            <td style="text-align: right;">₱{{ number_format($payslip->basic_pay, 2) }}</td>
                            <td style="text-align: right;">₱{{ number_format($payslip->overtime_pay, 2) }}</td>
                            <td style="text-align: right;">₱{{ number_format($payslip->total_deductions, 2) }}</td>
                            <td style="text-align: right;">₱{{ number_format($payslip->net_salary, 2) }}</td>
                            <td>{{ ucfirst($payslip->payment_status) }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('payslips.show', $payslip->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
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

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $payslips->appends(request()->query())->links() }}
            </div>
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
    <script>
        function confirmMarkAllPaid() {
            if (confirm('Are you sure you want to mark all pending payslips as paid?')) {
                document.getElementById('markAllPaidForm').submit();
            }
        }
    </script>
</body>
</html> 