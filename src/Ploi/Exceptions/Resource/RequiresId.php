<?php
declare(strict_types=1);

namespace Ploi\Exceptions\Resource;

use Exception;

class RequiresId extends Exception
{
    /**
     * RequiresId constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = "This action requires an ID to be set")
    {
        parent::__construct($message);
    }
}
