<?php

namespace Ploi;

use GuzzleHttp\Client;

class Ploi
{
    /**
     * Guzzle Client
     *
     * @var \GuzzleHttp\Client
     */
    public $guzzle;

    /**
     * Base API url
     *
     * @var $url
     */
    public $url = 'https://ploi.io/api';

    /**
     * Ploi constructor.
     *
     * @param $token
     */
    public function __construct($token)
    {
        $this->guzzle = new Client([
            'base_uri' => $this->url,
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
    }
}
