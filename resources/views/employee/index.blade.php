@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Employee Dashboard</h1>
    
    <div class="row">
        <!-- Attendance Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Attendance</h5>
                    <p class="card-text">Record your daily attendance and view history.</p>
                    <a href="{{ route('employee.attendance.create') }}" class="btn btn-primary mb-2 w-100">Record Attendance</a>
                    <a href="{{ route('employee.attendance.index') }}" class="btn btn-outline-primary w-100">View History</a>
                </div>
            </div>
        </div>

        <!-- Payslips Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Payslips</h5>
                    <p class="card-text">Access your payslip records and history.</p>
                    <a href="{{ route('employee.payslips.index') }}" class="btn btn-primary w-100">View Payslips</a>
                </div>
            </div>
        </div>

        <!-- Loans Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Loans</h5>
                    <p class="card-text">View your current loans and payment history.</p>
                    <a href="{{ route('employee.loans.index') }}" class="btn btn-primary w-100">View Loans</a>
                </div>
            </div>
        </div>

        <!-- Contributions Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Contributions</h5>
                    <p class="card-text">View your contribution records and history.</p>
                    <a href="{{ route('employee.contributions') }}" class="btn btn-primary w-100">View Contributions</a>
                </div>
            </div>
        </div>

        <!-- Profile Card -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Profile</h5>
                    <p class="card-text">
                        <strong>Name:</strong> {{ Auth::user()->name }}<br>
                        <strong>Position:</strong> {{ Auth::user()->employee->position->name }}<br>
                        <strong>Email:</strong> {{ Auth::user()->email }}
                    </p>
                    <a href="{{ route('employee.change-password.form') }}" class="btn btn-outline-primary w-100">Change Password</a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mt-4">
            {{ session('success') }}
        </div>
    @endif
</div>
@endsection 
