<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;

class BankingController extends Controller
{
    // public function showAllTransactions()
    // {
    //     // Get the authenticated user's ID and balance
    //     $user_id = auth()->user()->id;
    //     $balance = auth()->user()->balance;

    //     // Fetch all transactions for the user
    //     $transactions = DB::table('transactions')
    //         ->where('user_id', $user_id)
    //         ->orderBy('date', 'desc')
    //         ->get();

    //     return view('transactions', compact('transactions', 'balance'));
    // }

    public function showDepositedTransactions()
    {
        // Fetch all deposited transactions for the user
        $user_id = auth()->user()->id;
        $account_type= auth()->user()->account_type;
        $deposits = DB::table('transactions')
            ->where('user_id', $user_id)
            ->where('transaction_type', 'deposit')
            ->orderBy('date', 'desc')
            ->get();
        // Fetch all users with account_type not equal to "admin"
        $users = User::where('account_type', '!=', 'admin')->get();
        return view('deposits', compact('account_type','deposits', 'users'));
    }

    public function deposit(Request $request)
    {
       
        // Validate the request
        $request->validate([
            'user_id' => 'required',
            'amount' => 'required|numeric|min:0.01',
        ]);
       // dd($request->user_id);
        // Get the  user's ID
        $user_id = $request->user_id;

        // Update the user's balance by adding the deposited amount
        DB::table('users')
            ->where('id', $user_id)
            ->increment('balance', $request->input('amount'));

        // Create a new transaction record
        DB::table('transactions')->insert([
            'user_id' => $user_id,
            'transaction_type' => 'deposit',
            'amount' => $request->input('amount'),
            'fee' => 0, // Assuming no fee for deposits
            'date' => now(),
        ]);

        return redirect()->route('deposits')->with('success', 'Deposit successful.');
    }

    public function showWithdrawalTransactions()
    {
        // Fetch all withdrawal transactions for the authenticated user
        $user_id = auth()->user()->id;
        $account_type = auth()->user()->account_type;
        $withdrawals = DB::table('transactions')
            ->where('user_id', $user_id)
            ->where('transaction_type', 'withdrawal')
            ->orderBy('date', 'desc')
            ->get();

        
        // Fetch all users with account_type not equal to "admin"
        $users = User::where('account_type', '!=', 'admin')->get();
       
        return view('withdrawals', compact('account_type','withdrawals', 'users'));
    }

    public function withdraw(Request $request)
    {
        // Validate the withdrawal request
        $request->validate([
            'user_id' => 'required',
            'amount' => 'required|numeric|min:0.01',
        ]);

        // Get the user's ID and account type
        $user_id = $request->user_id;
        
        // Assuming $user_id is the ID of the user you want to retrieve
       $user = User::find($user_id);
        if ($user) {
            $account_type = $user->account_type;
            // Now $account_type contains the account type of the user with the specified $user_id
        } else {
            // Handle the case where the user with the specified ID is not found
        }
     
        // Calculate the withdrawal fee based on account type
        $withdrawal_fee = 0;
        if ($account_type === 'Individual') 
        {
            // Apply free withdrawal conditions for Individual accounts
            $today = now();
            if ($today->dayOfWeek === 5) {
                // Each Friday withdrawal is free of charge
            } elseif ($request->input('amount') > 1000) {
                // The first 1K withdrawal per transaction is free
                $withdrawal_fee = ($request->input('amount') - 1000) * 0.015 / 100;
            } elseif ($this->getTotalWithdrawalsForUserThisMonth($user_id) > 5000) {
                // The first 5K withdrawal each month is free
                $withdrawal_fee = $request->input('amount') * 0.015 / 100;
            }
           // dd($withdrawal_fee);
        }      
        elseif ($account_type === 'Business') 
        {
            // Apply changing withdrawal fees for Business accounts
            // Implement logic for changing fees after 50K withdrawals
            $total_withdrawals = $this->getTotalWithdrawalsForUser($user_id);
            if ($total_withdrawals > 50000) {
                // Decrease the withdrawal fee to 0.015% for Business accounts
                $withdrawal_fee = $request->input('amount') * 0.015 / 100;
            } else {
                // Apply the standard withdrawal fee for Business accounts
                $withdrawal_fee = $request->input('amount') * 0.025 / 100;
            }
        }

        // Calculate the total amount to deduct (including the withdrawal fee)
        $total_amount_to_deduct = $request->input('amount') + $withdrawal_fee;

        // Check if the user has enough balance for the withdrawal
        $user = User::find($user_id);
        if ($user->balance < $total_amount_to_deduct) {
            return redirect()->route('withdrawals')->with('error', 'Insufficient balance for withdrawal.');
        }

        // Update the user's balance by deducting the total amount
        $user->balance -= $total_amount_to_deduct;
        $user->save();

        // Create a new transaction record for the withdrawal
        DB::table('transactions')->insert([
            'user_id' => $user_id,
            'transaction_type' => 'withdrawal',
            'amount' => $request->input('amount'),
            'fee' => $withdrawal_fee,
            'date' => now(),
        ]);
       // dd($withdrawal_fee);

        return redirect()->route('withdrawals')->with('success', 'Withdrawal successful. Withdrawal Fee: $' . number_format($withdrawal_fee, 2));
    }

    // Helper function to calculate total withdrawals for a user
    private function getTotalWithdrawalsForUser($user_id)
    {
        return DB::table('transactions')
            ->where('user_id', $user_id)
            ->where('transaction_type', 'withdrawal')
            ->sum('amount');
    }

}
