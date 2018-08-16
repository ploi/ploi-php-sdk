<?php

namespace Ploi\Exceptions\Http;

use Exception;

/**
 * Class Unauthenticated
 *
 * @package Ploi\Exceptions\Http
 */
class Unauthenticated extends Exception
{

    /**
     * InternalServerError constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = "Cannot authenticate with Ploi")
    {
        parent::__construct($message);
    }
}
