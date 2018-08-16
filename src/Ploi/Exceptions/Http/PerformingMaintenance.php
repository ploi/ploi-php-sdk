<?php

namespace Ploi\Exceptions\Http;

use Exception;

/**
 * Class PerformingMaintenance
 *
 * @package Ploi\Exceptions\Http
 */
class PerformingMaintenance extends Exception
{

    /**
     * PerformingMaintenance constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = "Ploi is performing maintenance")
    {
        parent::__construct($message);
    }
}
