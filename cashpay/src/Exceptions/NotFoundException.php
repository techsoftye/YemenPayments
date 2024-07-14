<?php

namespace Techsoft\Cashpay\Exceptions;

use Exception;

class NotFoundException extends Exception
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
    public function __construct($requestId)
    {
        parent::__construct('The resource you are looking for could not be found.');

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
