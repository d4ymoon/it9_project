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
<div class="container py-4">
    <h1 class="mb-4">My Payslips</h1>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Basic Pay</th>
                            <th>Overtime Pay</th>
                            <th>Deductions</th>
                            <th>Net Pay</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payslips as $payslip)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse(explode('_', $payslip->pay_period)[0])->format('F Y') }}</td>
                            <td>₱{{ number_format($payslip->basic_pay, 2) }}</td>
                            <td>₱{{ number_format($payslip->overtime_pay, 2) }}</td>
                            <td>₱{{ number_format($payslip->total_deductions, 2) }}</td>
                            <td>₱{{ number_format($payslip->net_pay, 2) }}</td>
                            <td>
                                <a href="{{ route('employee.payslips.show', $payslip->id) }}" class="btn btn-sm btn-primary">
                                    View Details
                                </a>
                                <a href="{{ route('payslips.download', $payslip->id) }}" class="btn btn-sm btn-secondary">
                                    Download PDF
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $payslips->links() }}
            </div>
        </div>
    </div>
</div>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>