<?php

namespace Techsoft\Cashpay\Exceptions;

use Exception;

class FailedActionException extends Exception
{
    /**
     * The request id used in request
     *
     * @var mixed
     */
    public $requestId;

    /**
     * Create a new exception instance.
     *
     * @return void
     */
    public function __construct($requestId, string $body)
    {
        parent::__construct($body);

        $this->requestId = $requestId;
    }

    /**
     * The requestId returned from the operation.
     *
     * @return array|null
     */
    public function requestId()
    {
        return $this->requestId;
    }
}
