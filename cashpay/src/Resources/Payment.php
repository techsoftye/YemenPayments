<?php

namespace  Techsoft\Cashpay\Resources;

class Payment extends Resource
{
    /**
     * The status of the payment (only when calling check status).
     *
     * @var string
     */
    public $Status;

    /**
     * The referance of the payment.
     *
     * @var string
     */
    public $TransactionRef;
}
