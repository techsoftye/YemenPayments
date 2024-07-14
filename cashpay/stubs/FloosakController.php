<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class FloosakController extends Controller
{
    protected $config;

    public function __construct()
    {
        $this->config = config('cashpay.floosak');
    }

    public function initPayment(Request $request)
    {
        if (auth()->user()) {
            try {
                $messages = [
                    'amount.required' => translate('Amount must be a valid Number'),
                    'payment_type_key.required' => translate('Payment Type must be a valid payment gateway'),
                    'payment_type_key.in' => translate('Payment Type must be one of these gateway keys: CASHPAY, JAWALIPAY, Flosak, OneCash, KurimiPay'),
                    'payment_type.required' => translate('Payment Type must be a valid payment gateway'),
                    'payment_type.in' => translate('Payment Type must be one of these gateway keys: CASHPAY, JAWALIPAY, Flosak, OneCash, KurimiPay'),
                    'amount.numeric' => translate('Amount must be numeric.'),
                    'amount.min' => translate('Amount must be numeric and greater than 100'),
                    'target_phone.required' => translate('Wallet Mobile Number is required'),
                    'target_phone.YemenMobile' => translate('Mobile number must be a correct Yemeni Mobile number starting with 73, 71, 77, 70, or 78 with 9 digits.'),
                ];

                $validator = Validator::make($request->all(), [
                    'item_description' => 'required|string|max:300',
                    'amount' => 'required|numeric|min:100',
                    'payment_type' => 'required|in:CASHPAY,JAWALIPAY,Flosak,OneCash,KurimiPay',
                    'payment_type_key' => 'required|in:CASHPAY,JAWALIPAY,Flosak,OneCash,KurimiPay',
                    'target_phone' => 'required|YemenMobile',
                    'purpose' => 'required|string|max:255',
                ], $messages);

                if ($validator->fails()) {
                    return response()->json([
                        'result' => false,
                        'message' => $validator->errors()
                    ]);
                }

                $amount = $request->amount;
                $target_phone = $request->target_phone;
                $item_description = $request->item_description;
                $purpose = $request->purpose;
                $timestamp = Carbon::now()->timestamp * 1000;

                $initPayments = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'x-channel' => 'merchant',
                    'Authorization' => 'Bearer ' . $this->config['token'],
                ])->post($this->config['url'] . 'merchant/p2mcl', [
                    'source_wallet_id' => $this->config['source_wallet_id'],
                    'request_id' => $timestamp,
                    'target_phone' => "967" . $target_phone,
                    'amount' => $amount,
                    'purpose' => $purpose,
                ]);

                if (!$initPayments->ok()) {
                    return response()->json([
                        'result' => false,
                        'message' => $initPayments->json()['message']
                    ]);
                }

                return [
                    'init_payment_response' => $initPayments->json(),
                    'transaction_reference_id' => $initPayments->json()['data']['id']
                ];

            } catch (\Exception $e) {
                return response()->json([
                    'result' => false,
                    'message' => $e->getMessage()
                ]);
            }
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    public function CheckPaymentStatus(Request $request)
    {
        if (auth()->user()) {
            try {
                $messages = [
                    'otp.required' => translate('OTP must be a valid number'),
                    'payment_type_key.required' => translate('Payment Type must be a valid payment gateway'),
                    'payment_type_key.in' => translate('Payment Type must be one of these gateway keys: CASHPAY, JAWALIPAY, Flosak, OneCash, KurimiPay'),
                    'payment_type.required' => translate('Payment Type must be a valid payment gateway'),
                    'payment_type.in' => translate('Payment Type must be one of these gateway keys: CASHPAY, JAWALIPAY, Flosak, OneCash, KurimiPay'),
                    'otp.numeric' => translate('OTP must be numeric.'),
                    'purchase_id.numeric' => translate('Purchase ID must be numeric.'),
                    'purchase_id.required' => translate('Purchase ID must be a valid number'),
                ];

                $validator = Validator::make($request->all(), [
                    'otp' => 'required|numeric',
                    'purchase_id' => 'required|numeric',
                    'payment_type' => 'required|in:CASHPAY,JAWALIPAY,Flosak,OneCash,KurimiPay',
                    'payment_type_key' => 'required|in:CASHPAY,JAWALIPAY,Flosak,OneCash,KurimiPay',
                ], $messages);

                if ($validator->fails()) {
                    return response()->json([
                        'result' => false,
                        'message' => $validator->errors()
                    ]);
                }

                $otp = $request->otp;
                $purchase_id = $request->purchase_id;
                $timestamp = Carbon::now()->timestamp * 1000;

                $confirmPayment = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'x-channel' => 'merchant',
                    'Authorization' => 'Bearer ' . $this->config['token'],
                ])->post($this->config['url'] . 'merchant/p2mcl/confirm', [
                    'otp' => $otp,
                    'purchase_id' => $purchase_id,
                ]);

                if (!$confirmPayment->ok()) {
                    return response()->json([
                        'result' => false,
                        'message' => $confirmPayment->json()['message']
                    ]);
                }

                return $confirmPayment->json();

            } catch (\Exception $e) {
                return response()->json([
                    'result' => false,
                    'message' => $e->getMessage()
                ]);
            }
        } else {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
    }
}
