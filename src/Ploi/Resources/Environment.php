<?php

namespace Ploi\Resources;

use Ploi\Http\Response;

class Environment extends Resource
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
        $this->setEndpoint(
            $this->getServer()->getEndpoint() .
            '/' .
            $this->getServer()->getId() .
            '/sites/' .
            $this->getSite()->getId() .
            '/env'
        );

        return $this;
    }

    public function get(): Response
    {
        return $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    public function update(string $content): Response
    {
        // Set the options
        $options = [
            'body' => json_encode([
                'content' => $content,
            ]),
        ];

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'patch', $options);
    }
}
