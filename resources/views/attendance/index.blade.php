<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Records</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h3>Attendance Records</h3>

        <table class="table table-bordered table-hover mt-3">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Log In Time</th>
                    <th>Log Out Time</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->id }}</td>
                        <td>{{ $attendance->employee->id ?? 'N/A' }}</td>
                        <td>{{ $attendance->employee->name ?? 'Unknown' }}</td>
                        <td>{{ $attendance->time_in }}</td>
                        <td>{{ $attendance->time_out ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($attendance->date)->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No attendance records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
