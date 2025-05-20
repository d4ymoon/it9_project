<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employees</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body class="default-padding theme1" style="background-color: 	#f8f9fa">


    <div class="container-fluid">
        <!-- Search and Add Row -->
        <div class="row mt-2 align-items-end">
            <div class="col-auto">
                <form action="{{ route('employees.index') }}" method="GET" class="row g-3 align-items-end">
                    <!-- Search -->
                    <div class="col-auto">
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Name or email...">
                            <button class="btn btn-dark" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-auto">
                        <a href="{{ route('employees.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Add Button -->
            <div class="col d-flex justify-content-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newEmployeeModal">
                    <i class="bi bi-plus-circle"></i> Add Employee
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="alert mt-2 alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert mt-2 alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert mt-2 alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row mt-2">
            <div class="col">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered">
                        <thead>
                            <tr>
                                <th style="width:">ID</th>
                                <th style="width:">Name</th>
                                <th style="width:">Email</th>
                                <th style="width:">Contact</th>
                                <th style="width:">Position</th>
                                <th style="width:">Shift</th>
                                <th style="width:">Payment Method</th>
                                <th style="width:">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($employees as $employee)
                                <tr>
                                    <td>{{ $employee->id }}</td>
                                    <td>{{ $employee->name }}</td>
                                    <td>{{ $employee->email }}</td>
                                    <td>{{ $employee->contact_number }}</td>
                                    <td>{{ $employee->position->name ?? 'N/A' }}</td>
                                    <td>{{ optional($shifts->find($employee->shift_id))->name }}</td>
                                    <td>{{ ucfirst($employee->payment_method) }}</td>

                                    <td class="text-nowrap">
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewEmployeeModal{{ $employee->id }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#editEmployeeModal{{ $employee->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>                                     
                                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Contribution Modal (per employee) -->
                                <div class="modal fade" id="editEmployeeContribution{{ $employee->id }}" tabindex="-1"
                                    aria-labelledby="editEmployeeContributionLabel{{ $employee->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Employee Contributions</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <form action="{{ route('contributions.store') }}" method="POST"
                                                        class="d-flex align-items-center">
                                                        @csrf
                                                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">

                                                        <select name="contribution_type_id" class="form-select form-select-sm me-2" style="width: 160px;" required>
                                                            <option value="" disabled selected>-- Select Type --</option>
                                                            @foreach($contributionTypes as $type)
                                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                            @endforeach
                                                        </select>

                                                        <select name="calculation_type" class="form-select form-select-sm me-2" style="width: 100px;" required>
                                                            <option value="percent" selected>Percent</option>
                                                            <option value="fixed">Fixed</option>
                                                        </select>

                                                        <input type="number" step="0.01" name="value" class="form-control form-control-sm me-2"
                                                            style="width: 120px;" placeholder="Value" required>

                                                        <button type="submit" class="btn btn-success btn-sm me-2">Add</button>
                                                    </form>

                                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                                        data-bs-target="#addContributionType">
                                                        + New Contribution Type
                                                    </button>
                                                </div>

                                                <div id="contributions_list">
                                                    @if($employee->contributions->isEmpty())
                                                        <p class="text-muted">No contributions added yet.</p>
                                                    @else
                                                        <ul class="list-group">
                                                            @foreach($employee->contributions as $contribution)
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <span>{{ $contribution->contributionType->name }}</span>
                                                                <div class="d-flex align-items-center" style="gap: 8px;">
                                                                    <form action="{{ route('contributions.update', $contribution) }}"
                                                                        method="POST" class="d-flex align-items-center">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <select name="calculation_type" class="form-select form-select-sm me-2" style="width: 100px;">
                                                                            <option value="fixed" {{ $contribution->calculation_type === 'fixed' ? 'selected' : '' }}>Fixed</option>
                                                                            <option value="percent" {{ $contribution->calculation_type === 'percent' ? 'selected' : '' }}>Percent</option>
                                                                        </select>
                                                                        <input type="number" step="0.01" name="value"
                                                                            value="{{ $contribution->value }}" class="form-control form-control-sm me-2"
                                                                            style="width: 100px;" required>
                                                                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                                                    </form>

                                                                    <form action="{{ route('contributions.destroy', $contribution->id) }}"
                                                                        method="POST">
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

                                <!-- View Employee Modal -->
                                <div class="modal fade" id="viewEmployeeModal{{ $employee->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Employee Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <label class="col-md-4 col-form-label">Name:</label>
                                                    <div class="col-md-8">
                                                        <p class="form-control-plaintext">{{ $employee->name }}</p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label class="col-md-4 col-form-label">Email:</label>
                                                    <div class="col-md-8">
                                                        <p class="form-control-plaintext">{{ $employee->email }}</p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label class="col-md-4 col-form-label">Contact Number:</label>
                                                    <div class="col-md-8">
                                                        <p class="form-control-plaintext">{{ $employee->contact_number }}</p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label class="col-md-4 col-form-label">Shift:</label>
                                                    <div class="col-md-8">
                                                        <p class="form-control-plaintext">
                                                            {{ optional($shifts->find($employee->shift_id))->name }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label class="col-md-4 col-form-label">Hire Date</label>
                                                    <div class="col-md-8">
                                                        <p class="form-control-plaintext">
                                                            {{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') : 'N/A' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label class="col-md-4 col-form-label">Payment Method:</label>
                                                    <div class="col-md-8">
                                                        <p class="form-control-plaintext">{{ ucfirst($employee->payment_method) }}</p>
                                                    </div>
                                                </div>

                                                @if($employee->payment_method == 'bank')
                                                    <div class="row">
                                                        <label class="col-md-4 col-form-label">Bank Name:</label>
                                                        <div class="col-md-8">
                                                            <p class="form-control-plaintext">{{ $employee->bank_name }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <label class="col-md-4 col-form-label">Bank Account Number:</label>
                                                        <div class="col-md-8">
                                                            <p class="form-control-plaintext">{{ $employee->bank_acct }}</p>
                                                        </div>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- Edit Employee Modal -->
                                <div class="modal fade" id="editEmployeeModal{{ $employee->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('employees.update', $employee->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Employee</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                             <div class="modal-body">
                                                    <div class="row mb-3">
                                                        <label for="name{{ $employee->id }}" class="col-md-4 col-form-label">Name</label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" id="name{{ $employee->id }}"
                                                                name="name" value="{{ $employee->name }}" required>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label for="email{{ $employee->id }}" class="col-md-4 col-form-label">Email</label>
                                                        <div class="col-md-8">
                                                            <input type="email" class="form-control" id="email{{ $employee->id }}"
                                                                name="email" value="{{ $employee->email }}" required>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label for="contact_number{{ $employee->id }}" class="col-md-4 col-form-label">Contact Number</label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" id="contact_number{{ $employee->id }}"
                                                                name="contact_number" value="{{ $employee->contact_number }}" required>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label for="shift_id{{ $employee->id }}" class="col-md-4 col-form-label">Shift</label>
                                                        <div class="col-md-8">
                                                            <select class="form-select" id="shift_id{{ $employee->id }}" name="shift_id" required>
                                                                @foreach($shifts as $shift)
                                                                    <option value="{{ $shift->id }}" {{ $employee->shift_id == $shift->id ? 'selected' : '' }}>
                                                                        {{ $shift->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label for="payment_method{{ $employee->id }}" class="col-md-4 col-form-label">Payment Method</label>
                                                        <div class="col-md-8">
                                                            <select class="form-select" id="payment_method{{ $employee->id }}" name="payment_method" required>
                                                                <option value="cash" {{ $employee->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                                                                <option value="bank" {{ $employee->payment_method == 'bank' ? 'selected' : '' }}>Bank</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <label for="status{{ $employee->id }}" class="col-md-4 col-form-label">Status</label>
                                                        <div class="col-md-8">
                                                            <select class="form-select" id="status{{ $employee->id }}" name="status" required>
                                                                <option value="active" {{ $employee->status === 'active' ? 'selected' : '' }}>Active</option>
                                                                <option value="inactive" {{ $employee->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3 bank-details{{ $employee->payment_method == 'cash' ? ' d-none' : '' }}">
                                                        <label for="bank_name{{ $employee->id }}" class="col-md-4 col-form-label">Bank Name</label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" id="bank_name{{ $employee->id }}"
                                                                name="bank_name" value="{{ $employee->bank_name }}">
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3 bank-details{{ $employee->payment_method == 'cash' ? ' d-none' : '' }}">
                                                        <label for="bank_acct{{ $employee->id }}" class="col-md-4 col-form-label">Bank Account Number</label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" id="bank_acct{{ $employee->id }}"
                                                                name="bank_acct" value="{{ $employee->bank_acct }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No employees found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $employees->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
 <!-- Add Contribution Type Modal (global, outside foreach) -->
 <div class="modal fade" id="addContributionType" tabindex="-1" aria-labelledby="addContributionTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('contributiontypes.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addContributionTypeModalLabel">Add New Contribution Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="contribution_type_name" class="form-label">Contribution Type Name</label>
                    <input type="text" name="name" class="form-control" id="contribution_type_name" placeholder="e.g. SSS" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>
    <!-- New Employee Modal -->
    <div class="modal fade" id="newEmployeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('employees.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="position_id" class="form-label">Position</label>
                            <select class="form-select" id="position_id" name="position_id" required>
                                <option value="">Select Position</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}">{{ $position->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="shift_id" class="form-label">Shift</label>
                            <select class="form-select" id="shift_id" name="shift_id" required>
                                <option value="">Select Shift</option>
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="hire_date" class="form-label">Hire Date</label>
                            <input type="date" class="form-control" id="hire_date" name="hire_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="cash">Cash</option>
                                <option value="bank">Bank</option>
                            </select>
                        </div>
                        <div class="mb-3 bank-details d-none">
                            <label for="bank_name" class="form-label">Bank Name</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name">
                        </div>
                        <div class="mb-3 bank-details d-none">
                            <label for="bank_acct" class="form-label">Bank Account Number</label>
                            <input type="text" class="form-control" id="bank_acct" name="bank_acct">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show/hide bank details based on payment method selection
        document.querySelectorAll('[id^="payment_method"]').forEach(select => {
            select.addEventListener('change', function() {
                const bankDetails = this.closest('.modal-body').querySelectorAll('.bank-details');
                bankDetails.forEach(div => {
                    if (this.value === 'bank') {
                        div.classList.remove('d-none');
                    } else {
                        div.classList.add('d-none');
                    }
                });
            });
        });
    </script>
</body>

</html>