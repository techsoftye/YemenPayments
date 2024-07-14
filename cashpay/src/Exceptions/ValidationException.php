<?php

namespace Techsoft\Cashpay\Exceptions;

use Exception;

class ValidationException extends Exception
{
    /**
     * The request id used in request
     *
     * @var mixed
     */
    public $requestId;

    /**
     * The array of errors.
     *
     * @var array
     */
    public $errors;

    /**
     * Create a new exception instance.
     *
     * @param  array  $errors
     * @return void
     */
    public function __construct($requestId, array $errors)
    {
        parent::__construct('The given data failed to pass validation.');

        $this->requestId = $requestId;
        $this->errors = $errors;
    }

    /**
     * The array of errors.
     *
     * @return array
     */
    public function errors()
    {
        return $this->errors;
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
