<?php

namespace Ploi\Resources;

use Ploi\Http\Response;
use Ploi\Traits\HasPagination;

class DatabaseUser extends Resource
{

    use HasPagination;

    public function __construct(Server $server, Database $database, ?int $id = null)
    {
        parent::__construct($server->getPloi(), $id);

        $this->setDatabase($database);

        $this->buildEndpoint();
    }

    public function buildEndpoint(): self
    {
        $this->setEndpoint($this->getDatabase()->getEndpoint() . '/users');

        if ($this->getId()) {
            $this->setEndpoint($this->getEndpoint() . '/' . $this->getId());
        }

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

    public function create(string $user, string $password, bool $remote = false, string $remoteIp = '%', bool $readOnly = false): Response
    {
        $this->setId(null);

        $options = [
            'body' => json_encode([
                'user' => $user,
                'password' => $password,
                'remote' => $remote,
                'remote_ip' => $remoteIp,
                'readonly' => $readOnly
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
