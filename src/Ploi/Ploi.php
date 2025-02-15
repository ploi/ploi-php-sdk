<?php

namespace Ploi;

use Exception;
use GuzzleHttp\Client;
use Ploi\Exceptions\Http\InternalServerError;
use Ploi\Exceptions\Http\NotAllowed;
use Ploi\Exceptions\Http\NotFound;
use Ploi\Exceptions\Http\NotValid;
use Ploi\Exceptions\Http\PerformingMaintenance;
use Ploi\Exceptions\Http\TooManyAttempts;
use Ploi\Exceptions\Http\Unauthenticated;
use Ploi\Http\Response;
use Ploi\Resources\Project;
use Ploi\Resources\Script;
use Ploi\Resources\Server;
use Ploi\Resources\StatusPage;
use Ploi\Resources\User;
use Ploi\Resources\WebserverTemplate;
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
    public function __construct(?string $token = null)
    {
        if ($token) {
            $this->setApiToken($token);
        }
    }

    /**
     * @param string $token
     * @return self
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
     * @param array<string, mixed> $options
     * @return Response
     * @throws NotFound
     * @throws NotAllowed
     * @throws TooManyAttempts
     * @throws PerformingMaintenance
     * @throws InternalServerError
     * @throws NotValid
     * @throws Exception
     */
    public function makeAPICall(string $url, string $method = 'get', array $options = []): Response
    {
        if (!in_array($method, ['get', 'post', 'patch', 'delete'])) {
            throw new Exception('Invalid method type');
        }

        /**
         * Because we're calling the method dynamically PHPStorm doesn't
         * know that we're getting a response back, so we manually
         * tell it what is returned.
         *
         * @var ResponseInterface $response
         */
        $response = $this->guzzle->{$method}($url, $options);

        switch ($response->getStatusCode()) {
            case 401:
                throw new Unauthenticated($response->getBody());
            case 404:
                throw new NotFound($response->getBody());
            case 405:
                throw new NotAllowed($response->getBody());
            case 422:
                throw new NotValid($response->getBody());
            case 429:
                throw new TooManyAttempts($response->getBody());
            case 500:
                throw new InternalServerError($response->getBody());
            case 503:
                throw new PerformingMaintenance($response->getBody());
        }

        return new Response($response);
    }

    /**
     * Returns a server resource
     *
     * @param int|null $id
     * @return Server
     */
    public function server(?int $id = null): Server
    {
        return new Server($this, $id);
    }

    public function servers(?int $id = null): Server
    {
        return $this->server($id);
    }

    public function project(?int $id = null): Project
    {
        return new Project($this, $id);
    }

    public function projects(?int $id = null): Project
    {
        return $this->project($id);
    }

    public function scripts(?int $id = null): Script
    {
        return new Script($this, $id);
    }

    public function statusPage(?int $id = null): StatusPage
    {
        return new StatusPage($this, $id);
    }

    public function user(): User
    {
        return new User($this);
    }

    public function webserverTemplates(?int $id = null): WebserverTemplate
    {
        return new WebserverTemplate($this, $id);
    }
}
