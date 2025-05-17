<?php

namespace Ploi\Resources;

use Ploi\Ploi;

class Synchronize extends Resource
{
    private $endpoint = 'synchronize';

    public function __construct(?Ploi $ploi = null, ?int $id = null)
    {
        parent::__construct($ploi, $id);

        $this->setEndpoint($this->endpoint);
    }

    public function servers()
    {
        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/servers');
    }
}
