<?php

namespace Techsoft\Cashpay;



use Exception;
use  Techsoft\Cashpay\Exceptions\FailedActionException;
use  Techsoft\Cashpay\Exceptions\NotFoundException;
use  Techsoft\Cashpay\Exceptions\TimeoutException;
use  Techsoft\Cashpay\Exceptions\UnexpectedExcpetion;
use  Techsoft\Cashpay\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface;

trait MakesHttpRequests
{
    protected $requestId;

    /**
     * Make a GET request to Tamkeen servers and return the response.
     *
     * @param  string  $uri
     * @return mixed
     */
    public function get($uri)
    {
        return $this->request('GET', $uri);
    }

    /**
     * Make a POST request to Tamkeen servers and return the response.
     *
     * @param  string  $uri
     * @param  array  $payload
     * @return mixed
     */
    public function post($uri, array $payload = [])
    {
        return $this->request('POST', $uri, $payload);
    }

    /**
     * Make a PUT request to Tamkeen servers and return the response.
     *
     * @param  string  $uri
     * @param  array  $payload
     * @return mixed
     */
    public function put($uri, array $payload = [])
    {
        return $this->request('PUT', $uri, $payload);
    }

    /**
     * Make a DELETE request to Tamkeen servers and return the response.
     *
     * @param  string  $uri
     * @param  array  $payload
     * @return mixed
     */
    public function delete($uri, array $payload = [])
    {
        return $this->request('DELETE', $uri, $payload);
    }

    /**
     * Make request to Tamkeen servers and return the response.
     *
     * @param  string  $verb
     * @param  string  $uri
     * @param  array  $payload
     * @return mixed
     */
    protected function request($verb, $uri, array $payload = [])
    {
        $timestamp = round(microtime(true) * 1000);
        $token = md5($this->serviceProviderId . $this->username . $timestamp);
        $this->requestId = strtoupper($this->username) . '_' . $timestamp . '_' . rand();

        $response = $this->guzzle->request($verb, $uri, [
            'json' => $payload + [
                'RequestID' => $this->requestId,
                'UserName' => $this->username,
                'SpId' => $this->serviceProviderId,
                'MDToken' => $token,
            ],
            'headers' => ['unixtimestamp' => $timestamp],
            'cert' => [$this->certificatePath, $this->certificatePassword],
            'curl' => [CURLOPT_SSLCERTTYPE => 'P12'], // Define it's a PFX key
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode < 200 || $statusCode > 299) {
            return $this->handleRequestError($response);
        }

        $responseBody = (string) $response->getBody();

        return json_decode($responseBody, true) ?: $responseBody;
    }

    /**
     * Handle the request error.
     *
     * @param  \Psr\Http\Message\ResponseInterface  $response
     * @return void
     *
     * @throws \Exception
     * @throws \ Techsoft\Cashpay\Exceptions\FailedActionException
     * @throws \ Techsoft\Cashpay\Exceptions\NotFoundException
     * @throws \ Techsoft\Cashpay\Exceptions\ValidationException
     */
    protected function handleRequestError(ResponseInterface $response)
    {
        if ($response->getStatusCode() == 422) {
            throw new ValidationException($this->requestId, json_decode((string) $response->getBody(), true));
        }

        if ($response->getStatusCode() == 404) {
            throw new NotFoundException($this->requestId);
        }

        if ($response->getStatusCode() == 400) {
            throw new FailedActionException($this->requestId, (string) $response->getBody());
        }

        throw new UnexpectedExcpetion($this->requestId, (string) $response->getBody());
    }

    /**
     * Retry the callback or fail after x seconds.
     *
     * @param  int  $timeout
     * @param  callable  $callback
     * @param  int  $sleep
     * @return mixed
     *
     * @throws \ Techsoft\Cashpay\Exceptions\TimeoutException
     */
    public function retry($timeout, $callback, $sleep = 5)
    {
        $start = time();

        beginning:

        if ($output = $callback()) {
            return $output;
        }

        if (time() - $start < $timeout) {
            sleep($sleep);

            goto beginning;
        }

        throw new TimeoutException($this->requestId, $output);
    }
}
