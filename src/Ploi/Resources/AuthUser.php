<?php

namespace Ploi\Resources;

use Ploi\Http\Response;
use Ploi\Traits\HasPagination;

class AuthUser extends Resource
{
    use HasPagination;

    public function __construct(Server $server, Site $site, ?int $id = null)
    {
        parent::__construct($server->getPloi());

        $this->setServer($server);
        $this->setSite($site);

        if ($id) {
            $this->setId($id);
        }

        $this->buildEndpoint();
    }

    public function buildEndpoint(): self
    {
        $endpoint = $this->getSite()->getEndpoint() . '/auth-users';

        if ($this->getId()) {
            $endpoint .= '/' . $this->getId();
        }

        $this->setEndpoint($endpoint);

        return $this;
    }

    public function get(?int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        $this->buildEndpoint();

        return (is_null($this->getId())) 
            ? $this->page()
            : $this->getPloi()->makeAPICall($this->getEndpoint()); 
    }

    public function create(string $name, string $password): Response
    {
        $options = [
            'body' => json_encode([
                'name' => $name,
                'password' => $password,
            ]),
        ];

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);
    }

    public function delete(?int $id = null): Response
    {
        $this->setIdOrFail($id);

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');
    }

}