<?php


namespace Ploi\Exceptions\Resource\Server\Service;

use Exception;

class RequiresServiceName extends Exception
{
    /**
     * RequiresServiceName constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = "Service name is required")
    {
        parent::__construct($message);
    }
}