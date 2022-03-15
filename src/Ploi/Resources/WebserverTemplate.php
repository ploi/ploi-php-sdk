<?php

namespace Ploi\Resources;

use Ploi\Http\Response;
use Ploi\Ploi;
use Ploi\Traits\HasPagination;

class WebserverTemplate extends Resource
{
    use HasPagination;

    private $endpoint = 'webserver-templates';

    public function __construct(Ploi $ploi = null, ?int $id = null)
    {
        parent::__construct($ploi, $id);

        $this->setEndpoint($this->endpoint);
    }

    public function get(?int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        if ($this->getId()) {
            $this->setEndpoint($this->getEndpoint() . '/' . $this->getId());
        }

        return $this->getPloi()->makeAPICall($this->getEndpoint());
    }
}