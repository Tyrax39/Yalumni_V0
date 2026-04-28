<?php

use App\Http\Controllers\Alumni\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//call back
Route::match(array('GET', 'POST'), 'verify', [OrderController::class, 'verify'])->name('payment.verify');

if (isAddonInstalled('ALUDONATION')) {
    Route::match(array('GET', 'POST'), 'donation-verify', [\App\Http\Controllers\addon\donation\frontend\DonationController::class, 'verify'])->name('donation-payment.verify');
}

if (isAddonInstalled('ALUCOMMITTEE')) {
    Route::match(array('GET', 'POST'), 'nomination-application-verify', [\App\Http\Controllers\addon\committee\alumni\NominationController::class, 'verify'])->name('nomination_apply.verify');
}

if (isAddonInstalled('ALUSAAS')) {
    Route::match(array('GET', 'POST'), 'subscription/verify', [\App\Http\Controllers\addon\saas\admin\OrderController::class, 'verify'])->name('subscription.payment.verify');
}
