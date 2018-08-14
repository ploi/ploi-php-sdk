<?php

namespace Ploi;

use Exception;
use GuzzleHttp\Client;
use Ploi\Exceptions\Http\InternalServerError;
use Ploi\Exceptions\Http\NotFound;
use Ploi\Exceptions\Http\NotValid;
use Ploi\Exceptions\Http\PerformingMaintenance;
use Ploi\Exceptions\Http\TooManyAttempts;
use Ploi\Exceptions\Http\Unauthenticated;
use Ploi\Resources\Server\Server;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Ploi
 *
 * @package Ploi
 */
class Ploi
{
    /**
     * Guzzle Client
     *
     * @var \GuzzleHttp\Client
     */
    private $guzzle;

    /**
     * Base API URL
     *
     * @var string
     */
    private $url = 'https://ploi.io/api/';

    /**
     * The API token
     *
     * @var string
     */
    private $apiToken;

    /**
     * Ploi constructor.
     *
     * @param string|null $token
     */
    public function __construct(string $token = null)
    {
        if ($token) {
            $this->setApiToken($token);
        }
    }

    /**
     * @param $token
     * @return Ploi
     */
    public function setApiToken($token): self
    {
        // Set the token
        $this->apiToken = $token;

        // Generate a new Guzzle client
        $this->guzzle = new Client([
            'base_uri'    => $this->url,
            'http_errors' => false,
            'headers'     => [
                'Authorization' => 'Bearer ' . $this->getApiToken(),
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ],
        ]);

        return $this;
    }

    /**
     * Returns the API Token
     *
     * @return string
     */
    public function getApiToken(): string
    {
        return $this->apiToken;
    }

    /**
     * @param string $url
     * @param string $method
     * @param array  $options
     * @return \Psr\Http\Message\ResponseInterface
     * @throws NotFound
     * @throws TooManyAttempts
     * @throws PerformingMaintenance
     * @throws InternalServerError
     * @throws NotValid
     * @throws Exception
     */
    public function makeAPICall(string $url, string $method = "get", array $options = []): ?array
    {
        if (!in_array($method, ['get', 'post', 'patch', 'delete'])) {
            throw new Exception("Invalid method type");
        }

        /**
         * Because we're calling the method dynamically PHPStorm doesn't
         * know that we're getting a response back, so we manually
         * tell it what is returned.
         *
         * @var $response ResponseInterface
         */
        $response = $this->guzzle->{$method}($url, $options);

        switch ($response->getStatusCode()) {
            case 401:
                throw new Unauthenticated($response->getBody());
                break;
            case 404:
                throw new NotFound($response->getBody());
                break;
            case 422:
                throw new NotValid($response->getBody());
                break;
            case 429:
                throw new TooManyAttempts($response->getBody());
                break;
            case 500:
                throw new InternalServerError($response->getBody());
                break;
            case 503:
                throw new PerformingMaintenance($response->getBody());
                break;
        }

        return [
            'json'     => json_decode($response->getBody())->data,
            'response' => $response,
        ];
    }

    /**
     * Returns a server resource
     *
     * @param int|null $id
     * @return Server
     */
    public function server(int $id = null)
    {
        return new Server($this, $id);
    }
}
