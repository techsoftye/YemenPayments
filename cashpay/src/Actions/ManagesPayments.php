<?php

namespace  Techsoft\Cashpay\Actions;

use  Techsoft\Cashpay\Resources\OperationStatusType;
use  Techsoft\Cashpay\Resources\Payment;

trait ManagesPayments
{
    /**
     * Create a new payment. API recommends a 2 minute delay between checks.
     *
     * @param  string  $phone
     * @param  int  $cvvKey
     * @param  int  $amount
     * @param  \ Techsoft\Cashpay\Resources\Currency  $currency
     * @param  string  $description
     * @param  bool  $wait
     * @param  int  $timeout
     * @return \ Techsoft\Cashpay\Resources\Payment
     */
    public function createPayment(string $phone, int $customerCashPayCode, int $amount, int $currency, string $description = null, $wait = false, $timeout = 900)
    {
        $response = $this->post('CashPay/InitPayment', [
            'TargetMSISDN' => $phone,
            'CustomerCashPayCode' => $customerCashPayCode,
            'Amount' => $amount,
            'CurrencyId' => $currency,
            'Desc' => $description,
        ]);

        if ($wait) {
            return $this->retry($timeout, function () use ($response) {
                $payment = $this->checkPayment($response['RequestId'], OperationStatusType::INITIALED);

                return $payment->isSuccess() ? $payment : null;
            }, 120);
        }

        return new Payment($response, $this);
    }

    /**
     * Confirm the given payment.
     *
     * @param  string  $ref
     * @param  int  $otp
     * @param  bool  $wait
     * @param  int  $timeout
     * @return \ Techsoft\Cashpay\Resources\Payment
     */
    public function confirmPayment(string $ref, int $otp, $wait = false, $timeout = 900)
    {
        $otp = md5($ref . $otp);

        $response = $this->post('CashPay/ConfirmPayment', [
            'TransactionRef' => $ref,
            'TRCode' => $otp,
        ]);

        if ($wait) {
            return $this->retry($timeout, function () use ($response) {
                $payment = $this->checkPayment($response['RequestId'], OperationStatusType::CONFIRMED);

                return $payment->isSuccess() ? $payment : null;
            }, 120);
        }

        return new Payment($response, $this);
    }

    /**
     * Check a payment instance.
     *
     * @param  string  $ref
     * @param  \ Techsoft\Cashpay\Resources\OperationStatusType $type
     * @return \ Techsoft\Cashpay\Resources\Payment
     */
    public function checkPayment(string $ref, string $type)
    {
        return new Payment($this->post('Operation/OperationStatus', [
            'RequestIDOfNeededOperation' => $ref,
            'Type' => $type,
        ]), $this);
    }
}
