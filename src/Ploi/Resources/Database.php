<?php

namespace Ploi\Resources;

use Ploi\Http\Response;
use Ploi\Traits\HasPagination;

class Database extends Resource
{
    use HasPagination;

    private $server;

    public function __construct(Server $server, int $id = null)
    {
        parent::__construct($server->getPloi(), $id);

        $this->setServer($server);

        $this->buildEndpoint();
    }

    public function buildEndpoint(): self
    {
        $this->setEndpoint($this->getServer()->getEndpoint() . '/' . $this->getServer()->getId() . '/databases');

        if ($this->getId()) {
            $this->setEndpoint($this->getEndpoint() . '/' . $this->getId());
        }

        if ($this->getAction()) {
            $this->setEndpoint($this->getEndpoint() . '/' . $this->getAction());
        }

        return $this;
    }

    public function get(int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        // Make sure the endpoint is built
        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    public function create(string $name, string $user, string $password): Response
    {
        // Remove the id
        $this->setId(null);

        // Set the options
        $options = [
            'body' => json_encode([
                'name' => $name,
                'user' => $user,
                'password' => $password,
            ]),
        ];

        // Build the endpoint
        $this->buildEndpoint();

        // Make the request
        $response = $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);

        // Set the id of the site
        $this->setId($response->getJson()->data->id);

        // Return the data
        return $response;
    }

    public function delete(int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');
    }

    public function acknowledge(string $name): Response
    {
        // Remove the id
        $this->setId(null);

        // Set the action
        $this->setAction('acknowledge');

        // Set the options
        $options = [
            'body' => json_encode([
                'name' => $name,
            ]),
        ];

        // Build the endpoint
        $this->buildEndpoint();

        // Make the request
        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);
    }

    public function forget(int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        // Set the action
        $this->setAction('forget');

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');
    }

    public function backups($id = null): DatabaseBackup
    {
        return new DatabaseBackup($this->getServer(),$this,$id);
    }
}
