<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (auth()->check()) {
            
            $user_id = auth()->user()->id;
    
            // Get the authenticated user's account_type
            $account_type = auth()->user()->account_type;
            //dd($account_type);

            if($account_type == "admin"){
                return view('home');
            }
            else{
                
                // Get the authenticated user's ID and balance
                $user_id = auth()->user()->id;
                $balance = auth()->user()->balance;

                // Fetch all transactions for the user
                $transactions = DB::table('transactions')
                    ->where('user_id', $user_id)
                    ->orderBy('date', 'desc')
                    ->get();

                return view('general_user_home', compact('transactions', 'balance'));
              
            }
           
        } else {
            // User is not authenticated
           
        }
    
        
    }
}
