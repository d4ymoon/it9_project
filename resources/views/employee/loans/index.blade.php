@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">My Loans</h1>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Loan Type</th>
                            <th>Amount</th>
                            <th>Monthly Payment</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Remaining Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loans as $loan)
                        <tr>
                            <td>{{ $loan->loan_type }}</td>
                            <td>₱{{ number_format($loan->amount, 2) }}</td>
                            <td>₱{{ number_format($loan->monthly_payment, 2) }}</td>
                            <td>{{ $loan->start_date }}</td>
                            <td>{{ $loan->end_date }}</td>
                            <td>
                                <span class="badge bg-{{ $loan->status == 'Active' ? 'primary' : 'success' }}">
                                    {{ $loan->status }}
                                </span>
                            </td>
                            <td>₱{{ number_format($loan->remaining_balance, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $loans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 