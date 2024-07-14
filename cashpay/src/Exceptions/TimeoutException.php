<?php

namespace Techsoft\Cashpay\Exceptions;

use Exception;

class TimeoutException extends Exception
{
    /**
     * The request id used in request
     *
     * @var mixed
     */
    public $requestId;

    /**
     * The output returned from the operation.
     *
     * @var array|null
     */
    public $output;

    /**
     * Create a new exception instance.
     *
     * @param  array|null  $output
     * @return void
     */
    public function __construct($requestId, array $output = null)
    {
        parent::__construct('Script timed out while waiting for the process to complete.');

        $this->requestId = $requestId;
        $this->output = $output;
    }

    /**
     * The output returned from the operation.
     *
     * @return array|null
     */
    public function output()
    {
        return $this->output;
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
