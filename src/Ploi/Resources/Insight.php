<?php

namespace Ploi\Resources;

use Ploi\Http\Response;
use Ploi\Traits\HasPagination;

class Insight extends Resource
{
    use HasPagination;

    private $server;

    public function __construct(Server $server, int $id = null)
    {
        parent::__construct($server->getPloi(), $id);

        $this->setServer($server);

        $this->buildEndpoint();
    }

    public function buildEndpoint(): self
    {
        $endpoint = $this->getServer()->getEndpoint() . '/' . $this->getServer()->getId() . '/insights';

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

        return $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    public function detail(?int $id = null): Response
    {
        $this->setIdOrFail($id);

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/detail');
    }

    public function automaticallyFix(?int $id = null): Response
    {
        $this->setIdOrFail($id);

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/automatically-fix', 'post');
    }

    public function ignore(?int $id = null): Response
    {
        $this->setIdOrFail($id);

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/ignore', 'post');
    }

    public function delete(?int $id = null): Response
    {
        $this->setIdOrFail($id);

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');
    }
}