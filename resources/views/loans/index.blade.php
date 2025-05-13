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
            <div class="col-4 d-flex justify-content-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newLoanModal">
                    + Add Loan
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
                                        Edit Loan
                                    </button>
                                    <form action="{{ route('loans.destroy', $loan) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this loan?')">Delete</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Loan Modal -->
                            <div class="modal fade" id="editLoanModal{{ $loan->id }}" tabindex="-1"
                                aria-labelledby="editLoanModalLabel{{ $loan->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('loans.update', $loan) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Loan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col">
                                                        <label for="employee_id{{ $loan->id }}" class="form-label">Employee</label>
                                                    </div>
                                                    <div class="col">
                                                        <select name="employee_id" id="employee_id{{ $loan->id }}" class="form-select" required>
                                                            @foreach($employees as $employee)
                                                                <option value="{{ $employee->id }}" 
                                                                    {{ $loan->employee_id == $employee->id ? 'selected' : '' }}>
                                                                    {{ $employee->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row mt-1">
                                                    <div class="col">
                                                        <label for="loan_type{{ $loan->id }}" class="form-label">Loan Type</label>
                                                    </div>
                                                    <div class="col">
                                                        <input type="text" class="form-control" name="loan_type" 
                                                            id="loan_type{{ $loan->id }}" value="{{ $loan->loan_type }}" required>
                                                    </div>
                                                </div>

                                                <div class="row mt-1">
                                                    <div class="col">
                                                        <label for="loan_amount{{ $loan->id }}" class="form-label">Loan Amount</label>
                                                    </div>
                                                    <div class="col">
                                                        <div class="input-group">
                                                            <span class="input-group-text">₱</span>
                                                            <input type="number" class="form-control" name="loan_amount" 
                                                                id="loan_amount{{ $loan->id }}" value="{{ $loan->loan_amount }}" 
                                                                step="0.01" min="0" required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mt-1">
                                                    <div class="col">
                                                        <label for="deduction_percentage{{ $loan->id }}" class="form-label">Deduction Percentage</label>
                                                    </div>
                                                    <div class="col">
                                                        <div class="input-group">
                                                            <input type="number" class="form-control" name="deduction_percentage" 
                                                                id="deduction_percentage{{ $loan->id }}" value="{{ $loan->deduction_percentage }}" 
                                                                step="0.01" min="1" max="50" required>
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                        <small class="text-muted">Maximum 50% of salary</small>
                                                    </div>
                                                </div>

                                                <div class="row mt-1">
                                                    <div class="col">
                                                        <label for="start_date{{ $loan->id }}" class="form-label">Start Date</label>
                                                    </div>
                                                    <div class="col">
                                                        <input type="date" class="form-control" name="start_date" 
                                                            id="start_date{{ $loan->id }}" value="{{ $loan->start_date->format('Y-m-d') }}" required>
                                                    </div>
                                                </div>

                                                <div class="row mt-1">
                                                    <div class="col">
                                                        <label for="status{{ $loan->id }}" class="form-label">Status</label>
                                                    </div>
                                                    <div class="col">
                                                        <select name="status" id="status{{ $loan->id }}" class="form-select" required>
                                                            <option value="active" {{ $loan->status === 'active' ? 'selected' : '' }}>Active</option>
                                                            <option value="paid" {{ $loan->status === 'paid' ? 'selected' : '' }}>Paid</option>
                                                            <option value="cancelled" {{ $loan->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update Loan</button>
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
            </div>
        </div>
    </div>

    <!-- New Loan Modal -->
    <div class="modal fade" id="newLoanModal" tabindex="-1" aria-labelledby="newLoanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('loans.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Loan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <label for="employee_id" class="form-label">Employee</label>
                            </div>
                            <div class="col">
                                <select name="employee_id" id="employee_id" class="form-select" required>
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mt-1">
                            <div class="col">
                                <label for="loan_type" class="form-label">Loan Type</label>
                            </div>
                            <div class="col">
                                <input type="text" class="form-control" name="loan_type" id="loan_type" 
                                    value="{{ old('loan_type') }}" required placeholder="e.g. Personal Loan, Car Loan">
                            </div>
                        </div>

                        <div class="row mt-1">
                            <div class="col">
                                <label for="loan_amount" class="form-label">Loan Amount</label>
                            </div>
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" name="loan_amount" id="loan_amount" 
                                        value="{{ old('loan_amount') }}" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-1">
                            <div class="col">
                                <label for="deduction_percentage" class="form-label">Deduction Percentage</label>
                            </div>
                            <div class="col">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="deduction_percentage" id="deduction_percentage" 
                                        value="{{ old('deduction_percentage') }}" step="0.01" min="1" max="50" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Maximum 50% of salary</small>
                            </div>
                        </div>

                        <div class="row mt-1">
                            <div class="col">
                                <label for="start_date" class="form-label">Start Date</label>
                            </div>
                            <div class="col">
                                <input type="date" class="form-control" name="start_date" id="start_date" 
                                    value="{{ old('start_date') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Loan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 