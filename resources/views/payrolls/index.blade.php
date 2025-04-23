<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payrolls</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
  </head>
  <body class="default-padding theme1">
  

  <div class="container-fluid">
    <!--- Search, Reset, Add --->
    <div class="row mt-2">
      <form action="" method="GET" class="col-auto d-flex justify-content-start align-items-center">
        <label for="searchInput" class="label me-2">Search:</label>
          <div class="input-group">
            <input type="text" class="form-control" id="searchInput" name="query" style="width: 190px;">
            <button class="btn btn-dark"><i class="bi bi-search"></i></button>
          </div>
        
      </form>
      <div class="col px-0 d-flex align-items-center">

      
       
      </div>
      <div class="col-3 d-flex justify-content-end">
        <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addDeductionTypeModal">
            Generate Payroll
          </button>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col">
            <table class="table table-striped table-hover table-bordered ">
                <thead>
                    <tr>
                        <th style="width:110px">Employee ID</th>
                        <th style="width:110px">Basic Pay</th>
                        <th style="width:120px">Overtime Pay</th>
                        <th style="width:140px">Total Deductions</th>
                        <th style="width:110px">Tax</th>
                        <th style="width:150px">Net Salary</th>
                        <th style="width:140px">Pay Period</th>                  
                        <th style="width:275px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                  @foreach ($payrolls as $payroll)
                  <tr>
                        <td>{{ $payroll->employee_id }}</td>
                        <td>{{ $payroll->basic_pay }}</td>
                        <td>{{ $payroll->overtime_pay }}</td>
                        <td>{{ $payroll->total_deductions }}</td>
                        <td>{{ $payroll->tax }}</td>
                        <td>{{ $payroll->net_salary }}</td>    
                        <td>{{ $payroll->pay_period }}</td> 

                        <td class="text-nowrap"  style="width:275px">
                          <a href="{{ route('payrolls.edit', $payroll->id) }}" class="btn btn-sm btn-secondary">
                            Button 1
                        </a>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editPayrollModal{{ $payroll->id }}">
                                Edit Payroll
                            </button>
                          <form action="{{ route('employees.destroy', $payroll->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this employee?');" style="display: inline-block; margin: 0;">   
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit" DISABLED>Delete</button>
                          </form>
                        </td>
                    </tr>


                    <div class="modal fade" id="editPayrollModal{{ $payroll->id }}" tabindex="-1" aria-labelledby="editPayrollModalLabel{{ $payroll->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg"> 
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPayrollModalLabel{{ $payroll->id }}">Edit Payroll for {{ $payroll->employee->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('payrolls.update', $payroll->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- Row -->
                    <div class="row">
                        <!-- Edit Payroll -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Edit Payroll</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="basic_pay" class="form-label">Basic Pay</label>
                                        <input type="number" class="form-control" id="basic_pay" name="basic_pay" value="{{ $payroll->basic_pay }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="overtime_pay" class="form-label">Overtime Pay</label>
                                        <input type="number" class="form-control" id="overtime_pay" name="overtime_pay" value="{{ $payroll->overtime_pay }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="total_deductions" class="form-label">Total Deductions</label>
                                        <input type="number" class="form-control" id="total_deductions" name="total_deductions" value="{{ $payroll->total_deductions }}" disabled>
                                    </div>

                                    <div class="mb-3">
                                        <label for="net_salary" class="form-label">Net Salary</label>
                                        <input type="number" class="form-control" id="net_salary" name="net_salary" value="{{ $payroll->net_salary }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="pay_period" class="form-label">Pay Period</label>
                                        <input type="text" class="form-control" id="pay_period" name="pay_period" value="{{ $payroll->pay_period }}" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>
                            </div>
                        </div>

                        <!-- Edit Deductions -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Edit Deductions</h5>
                                </div>
                                <div class="card-body">

                                    <form action="{{ route('deductions.store') }}" method="POST" class="">
                                        @csrf
                                        <input type="hidden" name="payroll_id" value="{{ $payroll->id }}">
                        
                                        <div class="d-flex align-items-center">
                                            <!-- Deduction Type Dropdown -->
                                            <select name="deduction_type_id" id="deduction_type" class="form-select form-select-sm" style="width: 180px; margin-right: 10px;" required>
                                                <option value="" disabled selected>-- Select Type --</option>
                                                @foreach($deductionTypes as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('deduction_type_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                            <!-- Deduction Amount Input -->
                                            <input type="number" step="0.01" name="amount" class="form-control form-control-sm" style="width: 120px; margin-right: 10px;" placeholder="Amount" required>
                        
                                            <!-- Add Deduction Button -->
                                            <button type="submit" class="btn btn-success btn-sm">Add</button>
                                        </div>
                                    </form>
                                                                 
                                    <div class="mt-2" id="deductions_list">
                                      @if($payroll->deductions->isEmpty())
                                          <p>No deductions added yet.</p>
                                      @else
                                          <ul class="list-group">
                                              @foreach($payroll->deductions as $deduction)
                                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                                      <!-- Deduction Type (Tax) -->
                                                      <span>{{ $deduction->deductionType->name }}</span> 
                                  
                                                      <!-- Editable Deduction Amount -->
                                                      <form action="{{ route('deductions.update', $deduction->id) }}" method="POST" class="d-flex align-items-center">
                                                          @csrf
                                                          @method('PUT')
                                                          
                                                          <input type="number" step="0.01" name="amount" value="{{ $deduction->amount }}" class="form-control form-control-sm" style="width: 100px; margin-right: 10px;" required>
                                  
                                                          <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                                      </form>
                                  
                                                      <!-- Delete Button -->
                                                      <form action="{{ route('deductions.destroy', $deduction->id) }}" method="POST" style="display:inline;">
                                                          @csrf
                                                          @method('DELETE')
                                                          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                      </form>
                                                  </li>
                                              @endforeach
                                          </ul>
                                      @endif
                                  </div>
                                  
                                  
                                </div>
                            </div>
                        </div>
                    </div> <!-- End of Row -->

                
            </div>
        </div>
    </div>
</div>
                    @endforeach
                </tbody>
            </table>
          
        </div>
    </div>
  </div>

<!-- Add Deduction Type Modal -->
<div class="modal fade" id="addDeductionTypeModal" tabindex="-1" aria-labelledby="addDeductionTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        
        <div class="modal-header">
          <h5 class="modal-title" id="addDeductionTypeModalLabel">Generate Payroll</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <form action="{{ route('deductiontypes.store') }}" method="POST">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label for="deduction_type_name" class="form-label">Deduction Type Name</label>
              <input type="text" name="name" class="form-control" id="deduction_type_name" placeholder="" required>
            </div>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add</button>
          </div>
        </form>
  
      </div>
    </div>
  </div>

<!-- Add Deduction Modal -->
<div class="modal fade" id="addDeductionModal" tabindex="-1" aria-labelledby="addDeductionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
      
        <div class="modal-header">
          <h5 class="modal-title" id="addDeductionModalLabel">Add Deduction to Payroll</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <form action="{{ route('deductions.store') }}" method="POST">
          @csrf
          <input 
  type="hidden" 
  name="payroll_id" 
  value="{{ $payroll->id ?? '' }}"
>
          
          <div class="modal-body">
            <div class="mb-3">
              <label for="deduction_type" class="form-label">Deduction Type</label>
              <select name="deduction_type_id" id="deduction_type" class="form-select" required>
                <option value="" disabled selected>-- Select Deduction Type --</option>
                @foreach($deductionTypes as $type)
                  <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
              </select>
            </div>
            
            <div class="mb-3">
              <label for="deduction_amount" class="form-label">Amount</label>
              <input type="number" step="0.01" name="amount" class="form-control" id="deduction_amount" placeholder="Enter deduction amount" required>
            </div>

            
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Add Deduction</button>
          </div>
        </form>
  
      </div>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
@if(session('open_deduction_modal'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalId = 'editPayrollModal{{ session("deduction_id") }}';
            var modalEl = document.getElementById(modalId);
            if (modalEl) {
                var modalInstance = new bootstrap.Modal(modalEl);
                modalInstance.show();
            }
        });
    </script>
@endif
</body>
</html>