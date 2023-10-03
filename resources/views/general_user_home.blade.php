@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                    @if($transactions !== null)
                    <!-- Display transactions if not null -->
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Transaction Type</th>
                                <th>Amount</th>
                                <!-- Add other table headers here -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>{{ $transaction->transaction_type }}</td>
                                    <td>{{ $transaction->amount }}</td>
                                    <!-- Add other table data here -->
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <!-- Display a message or alternative content when transactions is null -->
                    <p>No transactions to display.</p>
                    @endif

                    <p>Current Balance: {{ $balance }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
