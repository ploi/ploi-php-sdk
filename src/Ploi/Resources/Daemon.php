<?php

namespace Ploi\Resources;

use Ploi\Http\Response;
use Ploi\Traits\HasPagination;

class Daemon extends Resource
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
        $this->setEndpoint($this->getServer()->getEndpoint() . '/' . $this->getServer()->getId() . '/daemons');

        if ($this->getId()) {
            $this->setEndpoint($this->getEndpoint() . '/' . $this->getId());
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

    public function create(string $command, string $systemUser, int $processes, ?string $directory = null): Response
    {
        // Remove the id
        $this->setId(null);

        // Set the options
        $options = [
            'body' => json_encode([
                'command' => $command,
                'system_user' => $systemUser,
                'processes' => $processes,
                'directory' => $directory,
            ]),
        ];

        // Build the endpoint
        $this->buildEndpoint();

        // Make the request
        $response = $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);

        $this->setId($response->getJson()->data->id);

        // Return the data
        return $response;
    }

    public function restart(int $id = null): Response
    {
        $this->setIdOrFail($id);

        // Build the endpoint
        $this->buildEndpoint();

        $this->setEndpoint($this->getEndpoint() . '/restart');

        // Make the request
        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'post');
    }

    public function pause(int $id = null): Response
    {
        $this->setIdOrFail($id);

        // Build the endpoint
        $this->buildEndpoint();

        $this->setEndpoint($this->getEndpoint() . '/toggle-pause');

        // Make the request
        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'post');
    }

    public function delete(int $id = null): Response
    {
        // Remove the id
        $this->setIdOrFail($id);

        // Build the endpoint
        $this->buildEndpoint();

        // Make the request
        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');
    }
}
