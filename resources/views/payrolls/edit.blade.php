<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
  </head>
  <body>
    <form method="POST" action="{{ route('payrolls.update', $payroll->id) }}">
        @csrf
        @method('PUT')
<div class="container mt-4">
    <h4>Edit Payroll for {{ $payroll->employee->name }}</h4>
        <!-- Row -->
        <div class="row">
            <!-- Edit Payroll -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Edit Payroll</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3 align-items-center">
                            <div class="col">
                                <label for="basic_pay" class="form-label mb-0">Basic Pay:</label>
                            </div>
                            <div class="col">
                                <input type="number" class="form-control" id="basic_pay" name="basic_pay" value="{{ $payroll->basic_pay }}" disabled>
                            </div> 
                        </div>


                        <div class="row mb-3 align-items-center">
                            <div class="col">
                            <label for="overtime_pay" class="form-label">Overtime Pay:</label>
                        </div>
                        <div class="col">
                            <input type="number" class="form-control" id="overtime_pay" name="overtime_pay" value="{{ $payroll->overtime_pay }}" required>
                        </div> </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col">
                            <label for="total_deductions" class="form-label">Total Deductions:</label>
                        </div>
                        <div class="col">
                            <input type="number" class="form-control" id="total_deductions" name="total_deductions" value="{{ $payroll->total_deductions }}" disabled>
                        </div></div>

                        <div class="row mb-3 align-items-center">
                            <div class="col">
                            <label for="taxable_income" class="form-label">Taxable Income:</label>
                        </div>
                        <div class="col">
                            <input type="number" class="form-control" id="taxable_income" name="taxable_income" value="{{ $payroll->taxable_income }}" disabled>
                        </div></div>

                        <div class="row mb-3 align-items-center">
                            <div class="col">
                            <label for="total_deductions" class="form-label">Tax:</label>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" id="tax" name="tax" value="{{ $payroll->tax == 0 ? 'Tax Not Applicable' : $payroll->tax }}" disabled>                        
                        </div></div>

                        <div class="row mb-3 align-items-center">
                            <div class="col">
                            <label for="net_salary" class="form-label">Net Salary:</label>
                        </div>
                        <div class="col">
                            <input type="number" class="form-control" id="net_salary" name="net_salary" value="{{ $payroll->net_salary }}" disabled>
                        </div></div>

                        <div class="row mb-3 align-items-center">
                            <div class="col">
                            <label for="pay_period" class="form-label">Pay Period:</label>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" id="pay_period" name="pay_period" value="{{ $payroll->pay_period }}" required>
                        </div> </div>
                    </div>
                    <button type="submit" class="btn btn-primary mx-3 mb-2">Save Changes</button>
                </form>
                </div>
            </div>

            <!-- Edit Deductions -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Edit Deductions</h5>
                    </div>
                    <div class="card-body">
                        <!--- Add Deduction Form -->
                        <div class="row">
                            <form action="{{ route('deductions.store') }}" method="POST" class="">
                                @csrf
                                <input type="hidden" name="payroll_id" value="{{ $payroll->id }}">
                
                                <div class="d-flex align-items-center col">
                                    <!-- Deduction Type Dropdown -->
                                    <select name="deduction_type_id" id="deduction_type" class="form-select form-select-sm" style="width: 150px; margin-right: 10px;" required>
                                        <option value="" disabled selected>-- Select Type --</option>
                                        @foreach($deductionTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('deduction_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <!-- Deduction Amount Input -->
                                    <input type="number" step="0.01" name="amount" class="form-control form-control-sm" style="width: 120px; margin-right: 11px;" placeholder="Amount" required>
                
                                    <!-- Add Deduction Button -->
                                    <button type="submit" class="btn btn-success btn-sm">Add</button>
                            </form>
                            <div class="vr mx-2"></div>
                            <div class="col">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addDeductionTypeModal">
                                    + New Deduction Type
                                </button>
                            </div>
                            </div>
                        </div>

                        <!-- Deductions List -->
                        <div class="mt-2" id="deductions_list">
                            @if($payroll->deductions->isEmpty())
                                <p>No deductions added yet.</p>
                            @else
                                <ul class="list-group">
                                    @foreach($payroll->deductions as $deduction)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <!-- Left: Deduction Type -->
                                            <span>{{ $deduction->deductionType->name }}</span>
                        
                                            <!-- Right: Amount input + Update + Delete -->
                                            <div class="d-flex align-items-center ms-auto" style="gap: 8px;">
                                                <!-- Update Form -->
                                                <form action="{{ route('deductions.update', $deduction->id) }}" method="POST" class="d-flex align-items-center">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="number" step="0.01" name="amount" value="{{ $deduction->amount }}" class="form-control form-control-sm" style="width: 100px;" required>
                                                    <button type="submit" class="btn btn-primary btn-sm ms-2">Update</button>
                                                </form>
                        
                                                <!-- Delete Form -->
                                                <form action="{{ route('deductions.destroy', $deduction->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div> 

        <!-- Add Deduction Type Modal -->
<div class="modal fade" id="addDeductionTypeModal" tabindex="-1" aria-labelledby="addDeductionTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm  modal-dialog-centered">
      <div class="modal-content">
        
        <div class="modal-header">
          <h5 class="modal-title" id="addDeductionTypeModalLabel">Add New Deduction Type</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <form action="{{ route('deductiontypes.store') }}" method="POST">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label for="deduction_type_name" class="form-label">Deduction Type Name</label>
              <input type="text" name="name" class="form-control" id="deduction_type_name" placeholder="e.g. SSS" required>
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
  </body>
</html>