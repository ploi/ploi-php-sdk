<?php

namespace Ploi\Resources;

use Ploi\Http\Response;

class Opcache extends Resource
{
    private $server;

    public function __construct(Server $server, ?int $id = null)
    {
        parent::__construct($server->getPloi(), $id);

        $this->setServer($server);

        $this->buildEndpoint();
    }

    public function buildEndpoint(): self
    {
        $this->setEndpoint($this->getServer()->getEndpoint() . '/' . $this->getServer()->getId());

        if ($this->getId()) {
            $this->setEndpoint($this->getEndpoint() . '/' . $this->getId());
        }

        return $this;
    }

    public function refresh()
    {
        $this->setEndpoint($this->getEndpoint() . '/refresh-opcache');

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'post');
    }

    public function enable()
    {
        $this->setEndpoint($this->getEndpoint() . '/enable-opcache');

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'post');
    }

    public function disable()
    {
        $this->setEndpoint($this->getEndpoint() . '/disable-opcache');

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'post');
    }
}
