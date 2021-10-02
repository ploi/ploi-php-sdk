<?php

namespace Ploi\Resources;

use Ploi\Http\Response;

class Robot extends Resource
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
        $this->setEndpoint($this->getSite()->getEndpoint());

        return $this;
    }

    public function allow(): Response
    {
        $options = [
            'body' => json_encode([
                'disable_robots' => false,
            ]),
        ];

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'patch', $options);
    }

    public function block(): Response
    {
        $options = [
            'body' => json_encode([
                'disable_robots' => true,
            ]),
        ];

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'patch', $options);
    }
}