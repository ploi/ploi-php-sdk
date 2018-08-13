<?php

namespace Ploi\Exceptions\Http;

use Exception;

/**
 * Class InternalServerError
 *
 * @package Ploi\Exceptions\Http
 */
class InternalServerError extends Exception
{

    /**
     * InternalServerError constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = "Ploi is having issues")
    {
        parent::__construct($message);
    }
}
