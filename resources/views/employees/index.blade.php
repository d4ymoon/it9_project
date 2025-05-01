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
                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                    data-bs-target="#newpositionmodal">
                    + Add Position
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newtenantmodal">
                    + Add Employees
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

    @if (session('info'))
        <div class="alert mt-2 alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
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
                        <th style="width:120px">ID</th>
                        <th style="width:110px">Contact #</th>
                        <th style="width:110px">Email</th>
                        <th style="width:220px">Name</th>
                        <th style="width:220px">Shift</th>
                        <th style="width:220px">Hire Date</th>
                        <th style="width:220px">Bank Account</th>
                        <th style="width:275px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                    <tr>
                        <td>{{ $employee->id }}</td>
                        <td>{{ $employee->contact_number }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->shift->name ?? 'No Shift Assigned' }}</td>
                        <td>{{ $employee->hire_date }}</td>
                        <td>{{ $employee->bank_acct }}</td>
                        <td class="text-nowrap" style="width:275px">
                            <!-- Edit Employee Button -->
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#editEmployeeModal{{ $employee->id }}">
                                Edit Employee
                            </button>
    
                            <!-- Edit Contribution Button -->
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editEmployeeContribution{{ $employee->id }}">
                                Edit Contributions
                            </button>
    
                            <!-- Delete Employee -->
                            <form action="{{ route('employees.destroy', $employee->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this employee?');"
                                style="display: inline-block; margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
    
                    <!-- Edit Employee Modal -->
                    <div class="modal fade" id="editEmployeeModal{{ $employee->id }}" tabindex="-1"
                        aria-labelledby="editEmployeeModalLabel{{ $employee->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('employees.update', $employee->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Employee</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="name{{ $employee->id }}" class="form-label">Name</label>
                                            <input type="text" class="form-control" name="name"
                                                id="name{{ $employee->id }}" value="{{ $employee->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="contact{{ $employee->id }}" class="form-label">Contact Number</label>
                                            <input type="text" class="form-control" name="contact_number"
                                                id="contact{{ $employee->id }}" value="{{ $employee->contact_number }}"
                                                maxlength="11" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email{{ $employee->id }}" class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email"
                                                id="email{{ $employee->id }}" value="{{ $employee->email }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="shift_id{{ $employee->id }}" class="form-label">Shift</label>
                                            <select class="form-select" name="shift_id" id="shift_id{{ $employee->id }}" required>
                                                @foreach ($shifts as $shift)
                                                    <option value="{{ $shift->id }}" {{ $employee->shift_id == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="bank{{ $employee->id }}" class="form-label">Bank Account</label>
                                            <input type="text" class="form-control" name="bank_acct"
                                                id="bank{{ $employee->id }}" value="{{ $employee->bank_acct }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Update Employee</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
    
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Add Contribution Type Modal (global, outside foreach) -->
    <div class="modal fade" id="addContributionType" tabindex="-1" aria-labelledby="addContributionTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
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
    
<!--- NEW EMPLOYEE MODAL --->
<div class="modal fade" id="newtenantmodal" tabindex="-1" aria-labelledby="exampleModalLabel"
aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Add new employee</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{ route('employees.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col">
                        <label for="name" class="form-label">Name:</label>
                    </div>
                    <div class="col">
                        <input class="form-control" type="text" name="name" id="name" required>
                    </div>
                </div>

                <div class="row mt-1">
                    <div class="col">
                        <label for="contact_number" class="form-label text-large">Contact Number:</label>
                    </div>
                    <div class="col">
                        <input class="form-control" type="text" name="contact_number" id="contact_number"
                            maxlength="11" required>
                    </div>
                </div>

                <div class="row mt-1">
                    <div class="col">
                        <label for="email" class="form-label">Email:</label>
                    </div>
                    <div class="col">
                        <input class="form-control" type="email" name="email" id="email" required>
                    </div>
                </div>

                <div class="row mt-1">
                    <div class="col">
                        <label for="position" class="form-label">Position:</label>
                    </div>
                    <div class="col">
                        <select class="form-control" name="position_id" id="position_id" required>
                            <option value="" disabled selected>Select Position</option>
                            @foreach ($positions as $position)
                                <option value="{{ $position->id }}">{{ $position->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-1">
                    <div class="col">
                        <label for="shift_id" class="form-label">Shift Type:</label>
                    </div>
                    <div class="col">
                        <select class="form-control" name="shift_id" id="shift_id" required>
                            <option value="" disabled selected>Select Shift</option>
                            @foreach ($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->name ?? 'Shift #' . $shift->id }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                

                <div class="row mt-1">
                    <div class="col">
                        <label for="hire_date" class="form-label">Hire Date:</label>
                    </div>
                    <div class="col">
                        <input class="form-control" type="date" name="hire_date" id="hire_date" required>
                    </div>
                </div>

                <div class="row mt-1">
                    <div class="col">
                        <label for="bank_acct" class="form-label">Bank Account:</label>
                    </div>
                    <div class="col">
                        <input class="form-control" type="text" name="bank_acct" id="bank_acct" required>
                    </div>
                </div>
        </div>
        <div class="modal-footer ">
            <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
</div>
</div>

<!--- NEW POSITION MODAL --->
<div class="modal fade" id="newpositionmodal" tabindex="-1" aria-labelledby="exampleModalLabel"
aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Add new position</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{ route('position.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col">
                        <label for="name" class="form-label">Name:</label>
                    </div>
                    <div class="col">
                        <input class="form-control" type="text" name="name" id="name" required>
                    </div>
                </div>

                <div class="row mt-1">
                    <div class="col">
                        <label for="contact_number" class="form-label text-large">Salary:</label>
                    </div>
                    <div class="col">
                        <input class="form-control" type="number" name="salary" id="salary"
                            step="0.01" required>
                    </div>
                </div>

        </div>
        <div class="modal-footer ">
            <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
</div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>