<?php

namespace Ploi\Http;

use Psr\Http\Message\ResponseInterface;
use stdClass;

/**
 * Class Response
 *
 * @package Ploi\Http
 */
class Response
{
    /**
     * @var stdClass|null
     */
    private $json;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * Response constructor.
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->setResponse($response);
        $this->decodeJson();
    }

    /**
     * Sets the Response from the Guzzle Client
     *
     * @param ResponseInterface $response
     * @return self
     */
    private function setResponse(ResponseInterface $response): self
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Returns the Response from the Guzzle Client
     *
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Recodes the body from the response
     *
     * @return self
     */
    private function decodeJson(): self
    {
        $json = json_decode($this->getResponse()->getBody());

        return $this->setJson($json);
    }

    /**
     * Sets the decoded JSON
     *
     * @param stdClass|null $json
     * @return self
     */
    public function setJson(stdClass $json = null): self
    {
        $this->json = $json;

        return $this;
    }

    /**
     * Gets the decoded json
     *
     * @return null|stdClass
     */
    public function getJson(): ?stdClass
    {
        return $this->json;
    }

    /**
     * Returns the JSON and Response as an array
     *
     * @return array{json: ?stdClass, response: ResponseInterface}
     */
    public function toArray(): array
    {
        return [
            'json'     => $this->getJson(),
            'response' => $this->getResponse(),
        ];
    }
}
