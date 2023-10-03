<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BankingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
// Specify a custom route name for registration
Route::name('custom.register')->get('/users',[App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//Route::get('/', [BankingController::class, 'showAllTransactions'])->name('transactions');
Route::get('/deposit', [BankingController::class, 'showDepositedTransactions'])->name('deposits')->middleware('auth');
Route::post('/deposit', [BankingController::class, 'deposit'])->name('deposit')->middleware('auth');
Route::get('/withdrawal', [BankingController::class, 'showWithdrawalTransactions'])->name('withdrawals')->middleware('auth');
Route::post('/withdrawal', [BankingController::class, 'withdraw'])->name('withdraw')->middleware('auth');
