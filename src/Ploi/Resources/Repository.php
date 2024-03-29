<?php

namespace Ploi\Resources;

use Ploi\Http\Response;

class Repository extends Resource
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
        $this->setEndpoint($this->getServer()->getEndpoint() . '/' . $this->getServer()->getId() . '/sites/' . $this->getSite()->getId() . '/repository');

        return $this;
    }

    public function get(): Response
    {
        return $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    public function install(string $provider, string $branch, string $name): Response
    {
        $options = [
            'body' => json_encode([
                'provider' => $provider,
                'branch' => $branch,
                'name' => $name
            ])
        ];

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);
    }

    public function delete(): Response
    {
        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');
    }

    public function toggleQuickDeploy(): Response
    {
        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/quick-deploy', 'post');
    }
}
