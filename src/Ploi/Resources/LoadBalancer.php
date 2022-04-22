<?php

namespace Ploi\Resources;

use Ploi\Http\Response;

class LoadBalancer extends Resource
{
    public function __construct(Server $server)
    {
        parent::__construct($server->getPloi());

        $this->setServer($server);

        $this->buildEndpoint();
    }

    public function buildEndpoint(): self
    {
        $this->setEndpoint($this->getServer()->buildEndpoint() . '/load-balancer');

        return $this;
    }

    public function requestCertificate(string $domain): Response
    {
        $url = $this->getEndpoint() . "/{$domain}/request-certificate";
        return $this->getPloi()->makeAPICall($url, 'post');
    }

    public function revokeCertificate(string $domain): Response
    {
        $url = $this->getEndpoint() . "/{$domain}/revoke-certificate";
        return $this->getPloi()->makeAPICall($url, 'delete');
    }
}