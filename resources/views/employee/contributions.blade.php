@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">My Contributions</div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Contribution Type</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contributions as $contribution)
                                    <tr>
                                        <td>{{ $contribution->created_at->format('M d, Y') }}</td>
                                        <td>{{ $contribution->contributionType->name }}</td>
                                        <td>₱{{ number_format($contribution->amount, 2) }}</td>
                                        <td>{{ $contribution->description }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No contributions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $contributions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 