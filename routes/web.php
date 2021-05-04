<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeController;
use App\Models\Customer;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/link_token', [StripeController::class, 'create_token']);
Route::post('/exchange-tokens', [StripeController::class, 'exchange_tokens'])->name('stripe.token');

Route::get('/customer', function () {
    $user = Customer::latest()->first();
    $details = $user->details;
    $json = json_decode($details);
    dd($json->id);
});
