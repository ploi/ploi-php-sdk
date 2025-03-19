<?php

namespace Ploi\Resources;

use Ploi\Http\Response;
use Ploi\Ploi;
use Ploi\Traits\HasPagination;

class StatusPage extends Resource
{
    use HasPagination;

    public function __construct(?Ploi $ploi = null, ?int $id = null)
    {
        parent::__construct($ploi, $id);

        $this->buildEndpoint();
    }

    public function buildEndpoint(): self
    {
        $endpoint = 'status-pages';

        if ($this->getId()) {
            $endpoint .= '/' . $this->getId();
        }

        $this->setEndpoint($endpoint);

        return $this;
    }

    public function get(?int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        $this->buildEndpoint();

        return (is_null($this->getId())) 
            ? $this->page()
            : $this->getPloi()->makeAPICall($this->getEndpoint()); 
    }

    public function incident(?int $id = null): Incident
    {
        return new Incident($this, $id);
    }
}