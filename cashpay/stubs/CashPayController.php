<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Techsoft\Cashpay\Tamkeen;

class CashPayController extends Controller
{
    protected $tamkeen;

    public function __construct()
    {
        $this->tamkeen = new Tamkeen(
            config('cashpay.cash.username'),
            config('cashpay.cash.password'),
            config('cashpay.cash.service_provider_id'),
            config('cashpay.cash.encryption_key'),
            storage_path('app/' . config('cashpay.cash.certificate_path')),
            config('cashpay.cash.certificate_password')
        );
        $this->tamkeen->build(config('cashpay.cash.port', 443));
    }

    public function initialpay(Request $request)
    {
        try {
            $response = $this->tamkeen->post('initial-payment-endpoint', $request->all());
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function CheckPaymentStatus(Request $request)
    {
        try {
            $response = $this->tamkeen->post('check-status-endpoint', $request->all());
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
