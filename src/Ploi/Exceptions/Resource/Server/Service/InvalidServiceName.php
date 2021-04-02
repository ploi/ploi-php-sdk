<?php


namespace Ploi\Exceptions\Resource\Server\Service;

use Exception;

class InvalidServiceName extends Exception
{
    /**
     * InvalidServiceName constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = "Service name is not valid")
    {
        parent::__construct($message);
    }
}