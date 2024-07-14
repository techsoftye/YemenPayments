<?php

namespace Techsoft\Cashpay;

use GuzzleHttp\Client as HttpClient;

class Tamkeen
{
    use MakesHttpRequests, Actions\ManageAccount, Actions\ManagesPayments;

    /**
     * The Tamkeen username.
     *
     * @var string
     */
    protected $username;

    /**
     * The Tamkeen password.
     *
     * @var string
     */
    protected $password;

    /**
     * The Tamkeen encryption key.
     *
     * @var string
     */
    protected $encryptionKey;

    /**
     * The Tamkeen service provider id (spId).
     *
     * @var string
     */
    protected $serviceProviderId;

    /**
     * The Tamkeen certificate path.
     *
     * @var string
     */
    protected $certificatePath;

    /**
     * The Tamkeen certificate password.
     *
     * @var string
     */
    protected $certificatePassword;

    /**
     * The Guzzle HTTP Client instance.
     *
     * @var \GuzzleHttp\Client
     */
    public $guzzle;

    /**
     * Number of seconds a request is retried.
     *
     * @var int
     */
    public $timeout = 30;

    /**
     * Create a new Tamkeen instance.
     *
     * @param  string|null  $apiKey
     * @param  \GuzzleHttp\Client|null  $guzzle
     * @return void
     */
    public function __construct($username = null, $password = null, $serviceProviderId = null, $encryptionKey = null, string $certificatePath = null, $certificatePassword = null, HttpClient $guzzle = null)
    {
        if (!is_null($username)) {
            $this->setUsername($username);
        }

        if (!is_null($password)) {
            $this->setPassword($password);
        }

        if (!is_null($serviceProviderId)) {
            $this->setServiceProviderId($serviceProviderId);
        }

        if (!is_null($encryptionKey)) {
            $this->setEncryptionKey($encryptionKey);
        }

        if (!is_null($certificatePath) && !is_null($certificatePassword)) {
            $this->setCertificate($certificatePath, $certificatePassword);
        }

        if (!is_null($guzzle)) {
            $this->guzzle = $guzzle;
        }
    }

    /**
     * Transform the items of the collection to the given class.
     *
     * @param  array  $collection
     * @param  string  $class
     * @param  array  $extraData
     * @return array
     */
    protected function transformCollection($collection, $class, $extraData = [])
    {
        return array_map(function ($data) use ($class, $extraData) {
            return new $class($data + $extraData, $this);
        }, $collection);
    }

    /**
     * Set the merchant username.
     *
     * @param  string $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set the merchant merchantpassword.
     *
     * @param  string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set the service provider id.
     *
     * @param  string $serviceProviderId
     * @return $this
     */
    public function setServiceProviderId($serviceProviderId)
    {
        $this->serviceProviderId = $serviceProviderId;

        return $this;
    }

    /**
     * Set the encryption key.
     *
     * @param  string $encryptionKey
     * @return $this
     */
    public function setEncryptionKey(string $encryptionKey)
    {
        $this->encryptionKey = $encryptionKey;

        return $this;
    }

    /**
     * Set the certificate path.
     *
     * @param  string $certificatePath
     * @param  mixed $certificatePassword
     * @return $this
     */
    public function setCertificate(string $certificatePath, $certificatePassword)
    {
        $this->certificatePath = $certificatePath;
        $this->certificatePassword = $certificatePassword;

        return $this;
    }

    /**
     * Set a new timeout.
     *
     * @param  int  $timeout
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Get the timeout.
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Build Tamkeen.
     *
     * @param  int  $port
     * @return $this
     */
    public function build(int $port)
    {
        $this->guzzle = $this->guzzle ?: new HttpClient([
            'base_uri' => "https://www.tamkeen.com.ye:{$port}/CashPG/api/",
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'encPassword' => $this->encryptPassword($this->encryptionKey, $this->password),
            ],
            'verify' => false,
        ]);

        return $this;
    }

    private function encryptPassword($key, $plaintext)
    {
        $method = 'aes-256-cbc';
        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);

        return base64_encode(openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv));
    }
}
