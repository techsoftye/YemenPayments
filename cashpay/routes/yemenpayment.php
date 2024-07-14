<?php

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\CashPayController;
use App\Http\Controllers\Payment\FloosakController;
use App\Http\Controllers\Payment\JawaliController;

Route::post('/yemen-payments/cashpay/intialpyament', [CashPayController::class, 'initialpay'])
    ->name('api.cashpay.initialpay')
    ->middleware('auth:sanctum');

Route::post('yemen-payments/cashpay/checkstatus', [CashPayController::class, 'CheckPaymentStatus'])
    ->name('api.CheckPaymentStauts')
    ->middleware('auth:sanctum');

Route::post('/yemen-payments/flooskpay/intialpyament', [FloosakController::class, 'initPayment'])
    ->name('api.floosak.initialpay')
    ->middleware('auth:sanctum');

Route::post('yemen-payments/flooskpay/checkstatus', [FloosakController::class, 'CheckPaymentStatus'])
    ->name('api.floosak.CheckPaymentStauts')
    ->middleware('auth:sanctum');

Route::post('/yemen-payments/jawalipay/intialpyament', [JawaliController::class, 'initPayment'])
    ->name('api.jawali.initialpay')
    ->middleware('auth:sanctum');

Route::post('yemen-payments/jawalipay/checkstatus', [JawaliController::class, 'confirmPayment'])
    ->name('api.jawali.CheckPaymentStauts')
    ->middleware('auth:sanctum');
