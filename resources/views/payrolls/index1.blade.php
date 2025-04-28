<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payrolls</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body class="default-padding theme1">

    <div class="container-fluid">
        <!--- Search and Reset --->
        <div class="row mt-2">
            <form action="" method="GET" class="col-auto d-flex justify-content-start align-items-center">
                <label for="searchInput" class="label me-2">Search:</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" name="query" style="width: 190px;">
                    <button class="btn btn-dark"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <div class="col d-flex justify-content-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generatePayrollModal">
                    + Generate Payroll
                </button>
            </div>
        </div>

        <!--- Payroll Table --->
        <div class="row mt-2">
            <div class="col">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Employee ID</th>
                            <th>Pay Period</th>
                            <th>Days Worked</th>
                            <th>Basic Pay</th>
                            <th>Overtime Pay</th>
                            <th>Total Deductions</th>
                            <th>Taxable Income</th>
                            <th>Tax</th>
                            <th>Net Salary</th>
                            <th style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payrolls as $payroll)
                            <tr>
                                <td>{{ $payroll->id }}</td>
                                <td>{{ $payroll->employee_id }}</td>
                                <td>{{ $payroll->pay_period }}</td>
                                <td>{{ $payroll->days_worked }}</td>
                                <td>{{ number_format($payroll->basic_pay, 2) }}</td>
                                <td>{{ number_format($payroll->overtime_pay, 2) }}</td>
                                <td>{{ number_format($payroll->total_deductions, 2) }}</td>
                                <td>{{ number_format($payroll->taxable_income, 2) }}</td>
                                <td>{{ number_format($payroll->tax, 2) }}</td>
                                <td>{{ number_format($payroll->net_salary, 2) }}</td>
                                <td class="text-nowrap">

                                    <!-- Example Edit Button -->
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editPayrollModal{{ $payroll->id }}">
                                        Edit
                                    </button>

                                    <!-- Delete Form -->
                                    <form action="{{ route('payrolls.destroy', $payroll->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this payroll record?');"
                                        style="display: inline-block; margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                    </form>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76A2z02tPqdjfv6jP6lG0FfDOMTD6zYtN6V8io9xMy5D/t93wCR0V5p5hfvZ1jr" crossorigin="anonymous">
    </script>
</body>

</html>
