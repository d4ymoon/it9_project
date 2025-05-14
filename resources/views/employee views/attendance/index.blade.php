<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attendance Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body class="default-padding theme1">

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!--- Controls Row --->
        <div class="row mt-3 align-items-end">
            <div class="col-auto">
                <form action="{{ route('attendances.index') }}" method="GET" class="row g-3 align-items-end">
                    <!-- Month Filter -->
                    <div class="col-auto">
                        <label for="month" class="form-label">Month:</label>
                        <input type="month" class="form-control" id="month" name="month" 
                               value="{{ request('month') }}"
                               min="{{ $minDate }}" max="{{ $maxDate }}">
                    </div>

                    <!-- Day Filter -->
                    <div class="col-auto">
                        <label for="date" class="form-label">Specific Day:</label>
                        <input type="date" class="form-control" id="date" name="date" 
                               value="{{ request('date') }}"
                               min="{{ Carbon\Carbon::parse($minDate)->startOfMonth()->toDateString() }}" 
                               max="{{ Carbon\Carbon::parse($maxDate)->endOfMonth()->toDateString() }}">
                    </div>

                    <!-- Search -->
                    <div class="col-auto">
                        <label for="search" class="form-label">Search Employee:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Employee name...">
                            <button class="btn btn-dark" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-auto">
                        <a href="{{ route('attendances.index') }}" class="btn btn-secondary">Reset Filters</a>
                    </div>
                </form>
            </div>

            <!-- Add Attendance Button (pushed to the right) -->
            <div class="col d-flex justify-content-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAttendanceModal">
                    <i class="bi bi-plus-circle"></i> Add Attendance
                </button>
            </div>
        </div>

        <!--- Attendance Table --->
        <div class="row mt-3">
            <div class="col">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Employee Shift</th>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Break Time Out</th>
                            <th>Break Time In</th>
                            <th>Time Out</th>
                            <th>Status</th>
                            <th style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attendances as $attendance)
                            <tr>
                                <td>{{ $attendance->employee->name }}</td>
                                <td>{{ $attendance->employee->shift->name ?? 'No Shift' }}</td>
                                <td>{{ Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}</td>
                                <td>{{ $attendance->time_in ? Carbon\Carbon::parse($attendance->time_in)->format('h:i A') : '-' }}</td>
                                <td>{{ $attendance->break_out ? Carbon\Carbon::parse($attendance->break_out)->format('h:i A') : '-' }}</td>
                                <td>{{ $attendance->break_in ? Carbon\Carbon::parse($attendance->break_in)->format('h:i A') : '-' }}</td>
                                <td>{{ $attendance->time_out ? Carbon\Carbon::parse($attendance->time_out)->format('h:i A') : '-' }}</td>
                                <td>{{ $attendance->status }}</td>
                                <td class="text-nowrap">
                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editAttendanceModal{{ $attendance->id }}">
                                        Edit
                                    </button>

                                    <!-- Delete Form -->
                                    <form action="{{ route('attendances.destroy', $attendance->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this attendance record?');"
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

                <div class="d-flex justify-content-center">
                    {{ $attendances->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Add Attendance Modal -->
    <div class="modal fade" id="addAttendanceModal" tabindex="-1" aria-labelledby="addAttendanceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAttendanceModalLabel">Add Attendance Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('attendances.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row mb-3">
                            <label for="employee_id" class="col-sm-4 col-form-label">Employee:</label>
                            <div class="col-sm-8">
                                <select class="form-select" id="employee_id" name="employee_id" required>
                                    <option value="">Select Employee</option>
                                    @foreach(\App\Models\Employee::orderBy('name')->get() as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="date" class="col-sm-4 col-form-label">Date:</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="time_in" class="col-sm-4 col-form-label">Time In:</label>
                            <div class="col-sm-8">
                                <input type="time" class="form-control" id="time_in" name="time_in">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="break_out" class="col-sm-4 col-form-label">Break Out:</label>
                            <div class="col-sm-8">
                                <input type="time" class="form-control" id="break_out" name="break_out">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="break_in" class="col-sm-4 col-form-label">Break In:</label>
                            <div class="col-sm-8">
                                <input type="time" class="form-control" id="break_in" name="break_in">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="time_out" class="col-sm-4 col-form-label">Time Out:</label>
                            <div class="col-sm-8">
                                <input type="time" class="form-control" id="time_out" name="time_out">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="status" class="col-sm-4 col-form-label">Status:</label>
                            <div class="col-sm-8">
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Present">Present</option>
                                    <option value="Absent">Absent</option>
                                    <option value="Half Day">Half Day</option>
                                    <option value="Leave">Leave</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Attendance Modals -->
    @foreach ($attendances as $attendance)
    <div class="modal fade" id="editAttendanceModal{{ $attendance->id }}" tabindex="-1" aria-labelledby="editAttendanceModalLabel{{ $attendance->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAttendanceModalLabel{{ $attendance->id }}">Edit Attendance - {{ $attendance->employee->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('attendances.update', $attendance->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row mb-3">
                            <label for="date{{ $attendance->id }}" class="col-sm-4 col-form-label">Date:</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control" id="date{{ $attendance->id }}" name="date" 
                                       value="{{ $attendance->date }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="time_in{{ $attendance->id }}" class="col-sm-4 col-form-label">Time In:</label>
                            <div class="col-sm-8">
                                <input type="time" class="form-control" id="time_in{{ $attendance->id }}" name="time_in" 
                                       value="{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '' }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="break_out{{ $attendance->id }}" class="col-sm-4 col-form-label">Break Out:</label>
                            <div class="col-sm-8">
                                <input type="time" class="form-control" id="break_out{{ $attendance->id }}" name="break_out" 
                                       value="{{ $attendance->break_out ? \Carbon\Carbon::parse($attendance->break_out)->format('H:i') : '' }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="break_in{{ $attendance->id }}" class="col-sm-4 col-form-label">Break In:</label>
                            <div class="col-sm-8">
                                <input type="time" class="form-control" id="break_in{{ $attendance->id }}" name="break_in" 
                                       value="{{ $attendance->break_in ? \Carbon\Carbon::parse($attendance->break_in)->format('H:i') : '' }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="time_out{{ $attendance->id }}" class="col-sm-4 col-form-label">Time Out:</label>
                            <div class="col-sm-8">
                                <input type="time" class="form-control" id="time_out{{ $attendance->id }}" name="time_out" 
                                       value="{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('H:i') : '' }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="status{{ $attendance->id }}" class="col-sm-4 col-form-label">Status:</label>
                            <div class="col-sm-8">
                                <select class="form-select" id="status{{ $attendance->id }}" name="status" required>
                                    <option value="Present" {{ $attendance->status == 'Present' ? 'selected' : '' }}>Present</option>
                                    <option value="Absent" {{ $attendance->status == 'Absent' ? 'selected' : '' }}>Absent</option>
                                    <option value="Half Day" {{ $attendance->status == 'Half Day' ? 'selected' : '' }}>Half Day</option>
                                    <option value="Leave" {{ $attendance->status == 'Leave' ? 'selected' : '' }}>Leave</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>
