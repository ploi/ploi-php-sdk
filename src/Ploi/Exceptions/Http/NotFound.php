<?php

namespace Ploi\Exceptions\Http;

use Exception;

/**
 * Class NotFound
 *
 * @package Ploi\Exceptions\Http
 */
class NotFound extends Exception
{

    /**
     * NotFound constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = "Endpoint not found")
    {
        parent::__construct($message);
    }
}
