@extends('layouts.app')

@section('content')
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

            <div class="mt-4">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 