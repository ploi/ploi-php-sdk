<?php


namespace Ploi\Resources;


use Ploi\Http\Response;

class Alias extends Resource
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
        $this->setEndpoint($this->getSite()->getEndpoint() . '/aliases');

        return $this;
    }

    public function get(): Response
    {
        return $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    public function create(array $aliases): Response
    {
        $options = [
            'body' => json_encode([
                'aliases' => $aliases,
            ]),
        ];

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);
    }

    public function delete(string $alias): Response
    {
        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/' . $alias , 'delete');
    }
}