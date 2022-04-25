<?php

namespace Ploi\Resources;

use Ploi\Http\Response;
use Ploi\Traits\HasPagination;

class Monitors extends Resource
{
    use HasPagination;

    public function __construct(Site $site, ?int $id = null)
    {
        parent::__construct($site->getPloi());

        $this->setSite($site);

        if ($id) {
            $this->setId($id);
        }

        $this->buildEndpoint();
    }

    public function buildEndpoint(): self
    {
        $endpoint = $this->getSite()->getEndpoint() . '/monitors';

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

    public function uptimeResponses(?int $id = null): Response
    {
        $this->setIdOrFail($id);

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/uptime-responses');
    }
}