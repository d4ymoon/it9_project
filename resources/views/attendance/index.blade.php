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

<body class="default-padding theme1" style="background-color: 	#f8f9fa">

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!--- Controls Row --->
        <div class="row mt-3 align-items-end">
            <div class="col-auto">
                <form action="{{ route('attendances.index') }}" method="GET" class="row g-3 align-items-end">
                    <!-- Month Filter -->
                    <div class="col-auto">
                        <div class="input-group">
                            <select class="form-select" id="month" name="month">
                                <option value="">All Months</option>
                                @php
                                    $start = Carbon\Carbon::parse($minDate);
                                    $end = Carbon\Carbon::parse($maxDate);
                                    $current = $start->copy();
                                @endphp
                                @while($current->lte($end))
                                    <option value="{{ $current->format('Y-m') }}" {{ request('month') == $current->format('Y-m') ? 'selected' : '' }}>
                                        {{ $current->format('F Y') }}
                                    </option>
                                    @php
                                        $current->addMonth();
                                    @endphp
                                @endwhile
                            </select>
                            <button class="btn btn-dark" type="submit"><i class="bi bi-funnel"></i> Filter</button>
                        </div>
                    </div>

                    <!-- Day Filter -->
                    <div class="col-auto">
                        <input type="date" class="form-control" id="date" name="date" 
                               value="{{ request('date') }}"
                               min="{{ Carbon\Carbon::parse($minDate)->startOfMonth()->toDateString() }}" 
                               max="{{ Carbon\Carbon::parse($maxDate)->endOfMonth()->toDateString() }}">
                    </div>

                    <!-- Search -->
                    <div class="col-auto">
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
                            <th>Total Hours</th>
                            <th>Regular Hours</th>
                            <th>Overtime Hours</th>
                            <th>Actions</th>
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
                                <td>{{ number_format($attendance->total_hours, 2) }}</td>
                                <td>{{ number_format($attendance->total_regular_hours, 2) }}</td>
                                <td>{{ number_format($attendance->total_overtime_hours, 2) }}</td>
                                <td class="text-nowrap">
                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editAttendanceModal{{ $attendance->id }}">
                                         <i class="bi bi-pencil"></i>
                                    </button>

                                    <!-- Delete Form -->
                                    <form action="{{ route('attendances.destroy', $attendance->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this attendance record?');"
                                        style="display: inline-block; margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" type="submit"><i class="bi bi-trash"></i></button>
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
                <form action="{{ route('attendances.adminadd') }}" method="POST">
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
                            <label for="status" class="col-sm-4 col-form-label">Status:</label>
                            <div class="col-sm-8">
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Present">Present</option>
                                    <option value="Leave">Leave</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="regular_hours" class="col-sm-4 col-form-label">Regular Hours:</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" id="regular_hours" name="regular_hours" min="0" max="24" step="0.5" value="8" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="overtime_hours" class="col-sm-4 col-form-label">Overtime Hours:</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" id="overtime_hours" name="overtime_hours" min="0" max="24" step="0.5" value="0" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">Total Hours:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="total_hours" readonly>
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
    <script>
        // Calculate total hours when regular or overtime hours change
        document.getElementById('regular_hours').addEventListener('input', calculateTotal);
        document.getElementById('overtime_hours').addEventListener('input', calculateTotal);

        function calculateTotal() {
            const regular = parseFloat(document.getElementById('regular_hours').value) || 0;
            const overtime = parseFloat(document.getElementById('overtime_hours').value) || 0;
            const total = regular + overtime;
            document.getElementById('total_hours').value = total.toFixed(1) + ' hours';
        }

        // Initialize total hours on page load
        calculateTotal();
    </script>
</body>

</html>
