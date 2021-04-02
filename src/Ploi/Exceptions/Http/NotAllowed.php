<?php

namespace Ploi\Exceptions\Http;

use Exception;

/**
 * Class NotAllowed
 *
 * @package Ploi\Exceptions\Http
 */
class NotAllowed extends Exception
{

    /**
     * NotAllowed constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = "Method not allowed")
    {
        parent::__construct($message);
    }
}
