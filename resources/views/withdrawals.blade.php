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

                    @if($account_type !== "admin")
                        <h1>Withdrawal Transactions</h1>

                        <!-- Display deposited transactions -->
                        @if(count($withdrawals) > 0)
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($withdrawals as $withdrawal)
                                        <tr>
                                            <td>{{ $withdrawal->id }}</td>
                                            <td>{{ $withdrawal->amount }}</td>
                                            <td>{{ $withdrawal->date }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>No deposited transactions to display.</p>
                        @endif
                    @endif

                    <!-- Deposit Form -->
                    @if($account_type == "admin")
                    <!-- Check if a success message exists in the session -->
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <h2>Make a Withdrawal</h2>
                    <form method="POST" action="{{ route('withdraw') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Select User</label>
                            <select class="form-control" id="user_id" name="user_id" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->id }} - {{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Withdraw</button>
                    </form>
                    @endif
    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
