<?php

namespace Ploi\Resources;

use Ploi\Http\Response;
use Ploi\Traits\HasPagination;

class Database extends Resource
{
    use HasPagination;

    public function __construct(Server $server, ?int $id = null)
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

    public function get(?int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        // Make sure the endpoint is built
        $this->buildEndpoint();

        return (is_null($this->getId())) 
            ? $this->page()
            : $this->getPloi()->makeAPICall($this->getEndpoint()); 
    }

    public function create(string $name, string $user, string $password, $description = null, $siteId = null): Response
    {
        // Remove the id
        $this->setId(null);

        // Set the options
        $options = [
            'body' => json_encode([
                'name' => $name,
                'user' => $user,
                'password' => $password,
                'description' => $description,
                'site_id' => $siteId,
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

    public function delete(?int $id = null): Response
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

    public function forget(?int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        // Set the action
        $this->setAction('forget');

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');
    }

    public function duplicate(string $name, ?string $user = null, ?string $password = null): Response
    {
        $this->setIdOrFail();

        $options = [
            'body' => json_encode([
                'name' => $name,
                'user' => $user,
                'password' => $password,
            ]),
        ];

        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/duplicate', 'post', $options);
    }

    public function backups($id = null): DatabaseBackup
    {
        return new DatabaseBackup($this->getServer(),$this,$id);
    }

    public function users(?int $id = null): DatabaseUser
    {
        return new DatabaseUser($this->getServer(), $this, $id);
    }
}
