<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employees</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body class="default-padding theme1">


    <div class="container-fluid">
        <!-- Search and Add Row -->
        <div class="row mt-2 align-items-end">
            <div class="col-auto">
                <form action="{{ route('employees.index') }}" method="GET" class="row g-3 align-items-end">
                    <!-- Search -->
                    <div class="col-auto">
                        <label for="search" class="form-label">Search Employee:</label>
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
                                <th style="width:80px">ID</th>
                                <th style="width:200px">Name</th>
                                <th style="width:200px">Email</th>
                                <th style="width:150px">Contact</th>
                                <th style="width:150px">Position</th>
                                <th style="width:120px">Hire Date</th>
                                <th style="width:150px">Payment Method</th>
                                <th style="width:200px">Actions</th>
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
                                    <td>{{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') : 'N/A' }}</td>
                                    <td>{{ ucfirst($employee->payment_method) }}</td>
                                    <td class="text-nowrap">
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#editEmployeeModal{{ $employee->id }}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>

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
                                                    <div class="mb-3">
                                                        <label for="name{{ $employee->id }}" class="form-label">Name</label>
                                                        <input type="text" class="form-control" id="name{{ $employee->id }}" 
                                                               name="name" value="{{ $employee->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="email{{ $employee->id }}" class="form-label">Email</label>
                                                        <input type="email" class="form-control" id="email{{ $employee->id }}" 
                                                               name="email" value="{{ $employee->email }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="contact_number{{ $employee->id }}" class="form-label">Contact Number</label>
                                                        <input type="text" class="form-control" id="contact_number{{ $employee->id }}" 
                                                               name="contact_number" value="{{ $employee->contact_number }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="shift_id{{ $employee->id }}" class="form-label">Shift</label>
                                                        <select class="form-select" id="shift_id{{ $employee->id }}" name="shift_id" required>
                                                            @foreach($shifts as $shift)
                                                                <option value="{{ $shift->id }}" {{ $employee->shift_id == $shift->id ? 'selected' : '' }}>
                                                                    {{ $shift->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="payment_method{{ $employee->id }}" class="form-label">Payment Method</label>
                                                        <select class="form-select" id="payment_method{{ $employee->id }}" name="payment_method" required>
                                                            <option value="cash" {{ $employee->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                                                            <option value="bank" {{ $employee->payment_method == 'bank' ? 'selected' : '' }}>Bank</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3 bank-details{{ $employee->payment_method == 'cash' ? ' d-none' : '' }}">
                                                        <label for="bank_name{{ $employee->id }}" class="form-label">Bank Name</label>
                                                        <input type="text" class="form-control" id="bank_name{{ $employee->id }}" 
                                                               name="bank_name" value="{{ $employee->bank_name }}">
                                                    </div>
                                                    <div class="mb-3 bank-details{{ $employee->payment_method == 'cash' ? ' d-none' : '' }}">
                                                        <label for="bank_acct{{ $employee->id }}" class="form-label">Bank Account Number</label>
                                                        <input type="text" class="form-control" id="bank_acct{{ $employee->id }}" 
                                                               name="bank_acct" value="{{ $employee->bank_acct }}">
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