<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Loans Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="default-padding theme1">
    <div class="container-fluid">
        <!-- Search and Filter Row -->
        <div class="row mt-2 align-items-end">
            <div class="col-auto">
                <form action="{{ route('loans.index') }}" method="GET" class="row g-3 align-items-end">
                    <!-- Search -->
                    <div class="col-auto">
                        <label for="search" class="form-label">Search Employee:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Employee name...">
                            <button class="btn btn-dark" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </div>

                    <!-- Loan Type Filter -->
                    <div class="col-auto">
                        <label for="loan_type" class="form-label">Loan Type:</label>
                        <select class="form-select" id="loan_type" name="loan_type">
                            <option value="">All Types</option>
                            <option value="Personal" {{ request('loan_type') == 'Personal' ? 'selected' : '' }}>Personal</option>
                            <option value="Emergency" {{ request('loan_type') == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                            <option value="Housing" {{ request('loan_type') == 'Housing' ? 'selected' : '' }}>Housing</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-auto">
                        <label for="status" class="form-label">Status:</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-auto">
                        <a href="{{ route('loans.index') }}" class="btn btn-secondary">Reset Filters</a>
                    </div>
                </form>
            </div>

            <!-- Add Button -->
            <div class="col d-flex justify-content-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newLoanModal">
                    <i class="bi bi-plus-circle"></i> Add Loan
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="alert mt-2 alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
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
                <div class="table-responsive mt-2">
                    <table class="table table-striped table-hover table-bordered">
                        <thead>
                            <tr>
                                <th style="width:80px">ID</th>
                                <th style="width:200px">Employee</th>
                                <th style="width:150px">Loan Type</th>
                                <th style="width:120px">Amount</th>
                                <th style="width:120px">Deduction %</th>
                                <th style="width:120px">Remaining</th>
                                <th style="width:120px">Start Date</th>
                                <th style="width:100px">Status</th>
                                <th style="width:200px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($loans as $loan)
                                <tr>
                                    <td>{{ $loan->id }}</td>
                                    <td>{{ $loan->employee->name }}</td>
                                    <td>{{ $loan->loan_type }}</td>
                                    <td>₱{{ number_format($loan->loan_amount, 2) }}</td>
                                    <td>{{ number_format($loan->deduction_percentage, 2) }}%</td>
                                    <td>₱{{ number_format($loan->remaining_balance, 2) }}</td>
                                    <td>{{ $loan->start_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $loan->status === 'active' ? 'success' : ($loan->status === 'paid' ? 'info' : 'danger') }}">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                    </td>
                                    <td class="text-nowrap">
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#editLoanModal{{ $loan->id }}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <form action="{{ route('loans.destroy', $loan) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this loan?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Loan Modal -->
                                <div class="modal fade" id="editLoanModal{{ $loan->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('loans.update', $loan->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Loan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="employee_id{{ $loan->id }}" class="form-label">Employee</label>
                                                        <select name="employee_id" id="employee_id{{ $loan->id }}" class="form-select" required>
                                                            @foreach($employees as $employee)
                                                                <option value="{{ $employee->id }}" 
                                                                    {{ $loan->employee_id == $employee->id ? 'selected' : '' }}>
                                                                    {{ $employee->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="loan_type{{ $loan->id }}" class="form-label">Loan Type</label>
                                                        <select name="loan_type" id="loan_type{{ $loan->id }}" class="form-select" required>
                                                            <option value="Personal" {{ $loan->loan_type == 'Personal' ? 'selected' : '' }}>Personal</option>
                                                            <option value="Emergency" {{ $loan->loan_type == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                                                            <option value="Housing" {{ $loan->loan_type == 'Housing' ? 'selected' : '' }}>Housing</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="loan_amount{{ $loan->id }}" class="form-label">Loan Amount</label>
                                                        <input type="number" step="0.01" class="form-control" id="loan_amount{{ $loan->id }}" 
                                                               name="loan_amount" value="{{ $loan->loan_amount }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="deduction_percentage{{ $loan->id }}" class="form-label">Deduction Percentage</label>
                                                        <input type="number" step="0.01" class="form-control" id="deduction_percentage{{ $loan->id }}" 
                                                               name="deduction_percentage" value="{{ $loan->deduction_percentage }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="start_date{{ $loan->id }}" class="form-label">Start Date</label>
                                                        <input type="date" class="form-control" id="start_date{{ $loan->id }}" 
                                                               name="start_date" value="{{ $loan->start_date->format('Y-m-d') }}" required>
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
                                    <td colspan="9" class="text-center">No loans found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $loans->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Loan Modal -->
    <div class="modal fade" id="newLoanModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('loans.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Loan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="employee_id" class="form-label">Employee</label>
                            <select name="employee_id" id="employee_id" class="form-select" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="loan_type" class="form-label">Loan Type</label>
                            <select name="loan_type" id="loan_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="Personal" {{ old('loan_type') == 'Personal' ? 'selected' : '' }}>Personal</option>
                                <option value="Emergency" {{ old('loan_type') == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                                <option value="Housing" {{ old('loan_type') == 'Housing' ? 'selected' : '' }}>Housing</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="loan_amount" class="form-label">Loan Amount</label>
                            <input type="number" step="0.01" class="form-control" id="loan_amount" 
                                   name="loan_amount" value="{{ old('loan_amount') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="deduction_percentage" class="form-label">Deduction Percentage</label>
                            <input type="number" step="0.01" class="form-control" id="deduction_percentage" 
                                   name="deduction_percentage" value="{{ old('deduction_percentage') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" 
                                   name="start_date" value="{{ old('start_date') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Loan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 