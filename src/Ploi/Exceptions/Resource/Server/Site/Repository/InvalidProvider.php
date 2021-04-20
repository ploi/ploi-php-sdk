<?php


namespace Ploi\Exceptions\Resource\Server\Site\Repository;

use Exception;

class InvalidProvider extends Exception
{
    /**
     * InvalidProvider constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = "Provider is not valid")
    {
        parent::__construct($message);
    }
}