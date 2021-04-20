<?php

namespace Ploi\Resources;

use Ploi\Http\Response;

class Deployment extends Resource
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
        $this->setEndpoint($this->getServer()->getEndpoint() . '/' . $this->getServer()->getId() . '/sites/' . $this->getSite()->getId());

        return $this;
    }

    public function deploy(): Response
    {
        $this->setEndpoint($this->getEndpoint() . '/deploy');

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'post');
    }

    public function deployToProduction(): Response
    {
        $this->setEndpoint($this->getEndpoint() . '/deploy-to-production');

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'post');
    }

    public function deployScript(): Response
    {
        $this->setEndpoint($this->getEndpoint() . '/deploy/script');

        return $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    public function updateDeployScript($script = ''): Response
    {
        $options = [
            'body' => json_encode([
                'deploy_script' => $script
            ]),
        ];

        $this->setEndpoint($this->getEndpoint() . '/deploy/script');

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'patch', $options);
    }
}
