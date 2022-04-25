<?php

namespace Ploi\Resources;

use Ploi\Http\Response;

class NginxConfiguration extends Resource
{
    public function __construct(Site $site)
    {
        parent::__construct($site->getPloi());

        $this->setSite($site);

        $this->buildEndpoint();
    }

    public function buildEndpoint(): self
    {
        $this->setEndpoint($this->getSite()->getEndpoint() . '/nginx-configuration');

        return $this;
    }

    public function get(): Response
    {
        return $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    public function update(string $configuration): Response
    {
        $options = [
            'body' => json_encode([
                'content' => $configuration,
            ]),
        ];
        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'patch', $options);
    }
}