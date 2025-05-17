<?php

namespace Ploi\Resources;

use Ploi\Http\Response;

class Tenant extends Resource
{
    public function __construct(Site $site)
    {
        parent::__construct($site->getPloi());

        $this->setSite($site);

        $this->buildEndpoint();
    }

    public function buildEndpoint(): self
    {
        $this->setEndpoint($this->getSite()->getEndpoint() . '/tenants');

        return $this;
    }

    public function get(): Response
    {
        return $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    public function create(array $tenants): Response
    {
        $options = [
            'body' => json_encode([
                'tenants' => $tenants,
            ])
        ];

        return $this->getPloi()->makeApiCall($this->getEndpoint(), 'post', $options);
    }

    public function delete(?string $tenant = null): Response
    {
        $url = "{$this->getEndpoint()}/{$tenant}";
        return $this->getPloi()->makeAPICall($url, 'delete');
    }

    public function requestCertificate(string $tenant, ?string $webhook = null, string $domains = ''): Response
    {
        $options = [
            'body' => json_encode([
                'webhook' => $webhook,
                'domains' => $domains
            ])
        ];

        $url = "{$this->getEndpoint()}/{$tenant}/request-certificate";

        return $this->getPloi()->makeApiCall($url, 'post', $options);
    }

    public function revokeCertificate(string $tenant, string $webhook): Response
    {
        $options = [
            'body' => json_encode([
                'webhook' => $webhook,
            ])
        ];

        $url = "{$this->getEndpoint()}/{$tenant}/revoke-certificate";

        return $this->getPloi()->makeApiCall($url, 'post', $options);
    }
}
