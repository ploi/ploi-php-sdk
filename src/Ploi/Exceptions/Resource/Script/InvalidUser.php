<?php


namespace Ploi\Exceptions\Resource\Script;

use Exception;

class InvalidUser extends Exception
{
    /**
     * InvalidUser constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = "User is not valid")
    {
        parent::__construct($message);
    }
}