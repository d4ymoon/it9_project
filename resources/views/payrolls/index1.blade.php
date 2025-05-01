<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payrolls</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
                    Generate Payroll
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

    <!-- Modal -->
<div class="modal fade" id="generatePayrollModal" tabindex="-1" aria-labelledby="generatePayrollModalLabel" aria-hidden="true">
    <div class="modal-dialog"> <!-- modal-lg for wider form -->
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="generatePayrollModalLabel">Generate Payroll</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
  
          <form action="{{ route('payrolls.generate') }}" method="POST">
            @csrf
  
            <!-- Payroll Frequency -->
            <div class="form-group">
              <label for="pay_frequency">Pay Frequency:</label>
              <select name="pay_frequency" id="pay_frequency" class="form-control" required>
                <option value="monthly" selected>Monthly</option>
                <option value="semi_monthly">Semi-Monthly</option>
              </select>
            </div>
  
            <!-- Semi-Monthly Option -->
            <div class="form-group mt-2" id="semiMonthlyOptions" style="display: none;">
              <label>Select Semi-Monthly Pay Period:</label>
              <div class="d-flex gap-2">
                <select name="pay_month" id="pay_month_semi" class="form-control" required>
                  <option value="" disabled selected>Month</option>
                  @foreach(range(1, 12) as $month)
                    <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}">
                      {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                    </option>
                  @endforeach
                </select>
  
                <select name="pay_year" id="pay_year_semi" class="form-control" required>
                  <option value="" disabled selected>Year</option>
                  @foreach(range(now()->year, now()->year + 5) as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                  @endforeach
                </select>
  
                <select name="pay_period_choice" id="pay_period_choice" class="form-control" required>
                  <option value="first_half">1st to 15th</option>
                  <option value="second_half">16th to End</option>
                </select>
              </div>
            </div>
  
            <!-- Monthly Option -->
            <div class="form-group mt-2" id="monthlyOptions" style="display: none;">
              <label>Select Month:</label>
              <div class="d-flex gap-2">
                <select name="pay_month" id="pay_month" class="form-control" required>
                  <option value="" disabled selected>Month</option>
                  @foreach(range(1, 12) as $month)
                    <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}">
                      {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                    </option>
                  @endforeach
                </select>
  
                <select name="pay_year" id="pay_year" class="form-control" required>
                  <option value="" disabled selected>Year</option>
                  @foreach(range(now()->year, now()->year + 5) as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                  @endforeach
                </select>
              </div>
            </div>
  
            <div class="modal-footer mt-3">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Generate Payroll</button>
            </div>
  
          </form>
  
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const currentDate = new Date();
      const currentMonth = String(currentDate.getMonth() + 1).padStart(2, '0');
      const currentYear = currentDate.getFullYear();
    
      document.getElementById('pay_month').value = currentMonth;
      document.getElementById('pay_year').value = currentYear;
    
      updatePayOptions();
    });
    
    function updatePayOptions() {
      const isSemiMonthly = document.getElementById('pay_frequency').value === 'semi_monthly';
    
      document.getElementById('semiMonthlyOptions').style.display = isSemiMonthly ? 'block' : 'none';
      document.getElementById('monthlyOptions').style.display = !isSemiMonthly ? 'block' : 'none';
    
      const monthlyInputs = document.querySelectorAll('#monthlyOptions select');
      const semiMonthlyInputs = document.querySelectorAll('#semiMonthlyOptions select');
    
      monthlyInputs.forEach(input => input.disabled = isSemiMonthly);
      semiMonthlyInputs.forEach(input => input.disabled = !isSemiMonthly);
    }
    
    document.getElementById('pay_frequency').addEventListener('change', updatePayOptions);
    </script>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>

</html>
