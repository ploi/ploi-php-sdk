<?php

namespace Ploi\Resources;

use Ploi\Ploi;
use Ploi\Http\Response;

class User extends Resource
{
    private $endpoint = 'user';

    public function __construct(Ploi $ploi = null)
    {
        parent::__construct($ploi);

        $this->setEndpoint($this->endpoint);
    }

    public function get(): Response
    {
        return $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    public function statistics(): Response
    {
        $url = $this->getEndpoint() . '/statistics';

        return $this->getPloi()->makeAPICall($url);
    }

    public function serverProviders(int $providerId = null): Response
    {
        $url = $this->getEndpoint() . '/server-providers';

        if ($providerId) {
            $url .= '/' . $providerId;
        }

        return $this->getPloi()->makeAPICall($url);
    }
}
