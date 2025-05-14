@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">My Payslips</h1>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Basic Pay</th>
                            <th>Overtime Pay</th>
                            <th>Deductions</th>
                            <th>Net Pay</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payslips as $payslip)
                        <tr>
                            <td>{{ date('F Y', strtotime($payslip->month)) }}</td>
                            <td>₱{{ number_format($payslip->basic_pay, 2) }}</td>
                            <td>₱{{ number_format($payslip->overtime_pay, 2) }}</td>
                            <td>₱{{ number_format($payslip->total_deductions, 2) }}</td>
                            <td>₱{{ number_format($payslip->net_pay, 2) }}</td>
                            <td>
                                <a href="{{ route('payslips.show', $payslip->id) }}" class="btn btn-sm btn-primary">
                                    View Details
                                </a>
                                <a href="{{ route('payslips.download', $payslip->id) }}" class="btn btn-sm btn-secondary">
                                    Download PDF
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $payslips->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 