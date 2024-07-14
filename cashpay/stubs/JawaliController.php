<?php

namespace App\Http\Controllers\Payment;

use Carbon\Carbon;
use App\Models\JawaliToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class JawaliController extends Controller
{
    private $config;
    private $oauth_token;
    private $wallet_token;

    public function __construct()
    {
        $this->config = config('cashpay.jawali');
    }

    private function hasAllValues(array $config): bool
    {
        return empty(array_filter($config, function ($value) {
            return $value === null || $value === '';
        }));
    }

    public function initPayment(Request $request)
    {
        $this->getOuthToken();

        try {
            $messages = [
                'amount.required' => 'Amount must be a valid Number',
                'jawali_pay_code.required' => 'Jawali pay code must be a valid Number',
                'payment_type_key.required' => 'Payment Type must be a valid payment gateway',
                'payment_type_key.in' => 'Payment Type must be one of these gateway keys: CASHPAY, JAWALIPAY, Flosak, OneCash, KurimiPay',
                'payment_type.required' => 'Payment Type must be a valid payment gateway',
                'payment_type.in' => 'Payment Type must be one of these gateway keys: CASHPAY, JAWALIPAY, Flosak, OneCash, KurimiPay',
                'amount.numeric' => 'Amount must be numeric.',
                'jawali_pay_code.numeric' => 'Jawali pay code must be numeric.',
                'amount.min' => 'Amount must be numeric and greater than 100',
                'target_phone.required' => 'Wallet Mobile Number is required',
                'target_phone.YemenMobile' => 'Mobile number must be a correct Yemeni Mobile number starting with 73, 71, 77, 70, or 78 with 9 digits.',
            ];

            $validator = Validator::make($request->all(), [
                'item_description' => 'required|string|max:300',
                'amount' => 'required|numeric|min:100',
                'jawali_pay_code' => 'required|numeric',
                'payment_type' => 'required|in:CASHPAY,JAWALIPAY,Flosak,OneCash,KurimiPay',
                'payment_type_key' => 'required|in:CASHPAY,JAWALIPAY,Flosak,OneCash,KurimiPay',
                'target_phone' => 'required|YemenMobile',
                'purpose' => 'required|string|max:255',
            ], $messages);

            if ($validator->fails()) {
                return response()->json(['result' => false, 'message' => $validator->errors()]);
            }

            $amount = $request->amount;
            $target_phone = $request->target_phone;
            $cash_pay_code = $request->jawali_pay_code;
            $item_description = $request->item_description ?? 'Payment for purchases';
            $purpose = $request->purpose;
            $timestamp = Carbon::now()->timestamp * 1000;

            $requestBody = $this->getRequestBody(
                'PAYAG.ECOMMERCEINQUIRY',
                'MerchantDomain',
                [
                    "agentWallet" => $this->config['agent_wallet'],
                    "voucher" => $cash_pay_code,
                    "receiverMobile" => $target_phone,
                    "password" => $this->config['wallet_password'],
                    "accessToken" => $this->wallet_token,
                    "refId" => (string) Carbon::now()->timestamp,
                    "purpose" => $purpose
                ]
            );

            $initPayment = Http::withoutVerifying()->timeout(60)
                ->withBody($requestBody, 'application/json')
                ->withToken($this->oauth_token)
                ->post($this->config['wallet_url']);

            if ($initPayment->successful() && $initPayment->json()['responseStatus']['systemStatus'] == -1) {
                return response()->json(['result' => false, 'message' => $initPayment->json()['responseStatus']]);
            }

            if ($initPayment->successful()) {
                if ($initPayment->json()['responseBody']['txnamount'] != $amount) {
                    return response()->json(['result' => false, 'message' => 'The Voucher Amount is not correct, please create a new voucher with an amount equal to ' . $amount]);
                }
                if ($initPayment->json()['responseBody']['state'] == 'EXPIRED') {
                    return response()->json(['result' => false, 'message' => 'The Voucher was EXPIRED, please create a new voucher and try again']);
                }
                if ($initPayment->json()['responseBody']['state'] == 'ACCEPTED') {
                    return response()->json(['result' => false, 'message' => 'The Voucher was ACCEPTED before, you cannot reuse it']);
                }

                return response()->json(['result' => true, 'init_payment_response' => $initPayment->json(), 'transaction_reference_id' => $initPayment->json()['responseBody']['issuerTrxRef'], 'message' => 'The Voucher is ready for payment, please send confirmation']);
            }
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getRequestBody(string $serviceName, string $domainName, array $body): string
    {
        $requestBody = [
            'header' => [
                'serviceDetail' => [
                    'corrID' => '59ba381c-1f5f-4480-90cc-0660b9cc850e',
                    'domainName' => $domainName,
                    'serviceName' => $serviceName,
                ],
                'signonDetail' => [
                    'clientID' => 'WeCash',
                    'orgID' => $this->config['org_id'],
                    'userID' => $this->config['user_name'],
                    'externalUser' => 'user1',
                ],
                'messageContext' => [
                    'clientDate' => (string) Carbon::now()->timestamp,
                    'bodyType' => 'Clear',
                ],
            ],
            'body' => $body
        ];
        return json_encode($requestBody);
    }
    
    public function getOuthToken()
    {
        try {
            $oauthResponse = Http::asForm()->withoutVerifying()->timeout(60)->post($this->config['oauth_url'], [
                'grant_type' => 'password',
                'client_id' => 'restapp',
                'client_secret' => 'restapp',
                'username' => $this->config['user_name'],
                'password' => $this->config['oauth_password'],
                'scope' => 'read',
            ]);

            if ($oauthResponse->successful()) {
                $walletToken = $this->getWalletToken($oauthResponse['access_token']);
                $this->oauth_token = $oauthResponse['access_token'];
                $this->wallet_token = $walletToken->json()['responseBody']['access_token'];
            } else {
                throw new \Exception('Error during OAuth token acquisition: ' . $oauthResponse->body());
            }
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getWalletToken($access_token)
    {
        try {
            $requestBody = $this->getRequestBody('PAYWA.WALLETAUTHENTICATION', 'WalletDomain', [
                'identifier' => $this->config['agent_wallet'],
                'password' => $this->config['wallet_password'],
            ]);

            $response = Http::withoutVerifying()->timeout(60)
                ->withBody($requestBody, 'application/json')
                ->withToken($access_token)
                ->post($this->config['wallet_url']);

            if ($response->successful()) {
                return $response;
            } else {
                throw new \Exception('Error during wallet token acquisition: ' . $response->body());
            }
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()]);
        }
    }
}
