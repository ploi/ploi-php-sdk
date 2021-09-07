<?php

namespace Ploi\Resources;

use Ploi\Http\Response;

class FastCgi extends Resource
{
    public function __construct(Server $server, Site $site)
    {
        parent::__construct($server->getPloi());

        $this->setServer($server);
        $this->setSite($site);

        $this->buildEndpoint();
    }

    public function buildEndpoint(): self
    {
        $this->setEndpoint($this->getSite()->getEndpoint() . '/fastcgi-cache');

        return $this;
    }

    public function enable(): Response
    {
        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/enable', 'post');
    }

    public function disable(): Response
    {
        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/disable', 'delete');
    }

    public function flush(): Response
    {
        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/flush', 'post');
    }

}