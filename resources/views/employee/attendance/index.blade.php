<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shifts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body class="default-padding theme1" style="background-color: 	#f8f9fa">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>My Attendance Records</h1>
        <a href="{{ route('employee.attendance.create') }}" class="btn btn-primary">Record Attendance</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Break Out</th>
                            <th>Break In</th>
                            <th>Time Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->date }}</td>
                            <td>{{ $attendance->time_in ? date('h:i A', strtotime($attendance->time_in)) : '-' }}</td>
                            <td>{{ $attendance->break_out ? date('h:i A', strtotime($attendance->break_out)) : '-' }}</td>
                            <td>{{ $attendance->break_in ? date('h:i A', strtotime($attendance->break_in)) : '-' }}</td>
                            <td>{{ $attendance->time_out ? date('h:i A', strtotime($attendance->time_out)) : '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $attendance->status == 'Present' ? 'success' : 'warning' }}">
                                    {{ $attendance->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
</div>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>