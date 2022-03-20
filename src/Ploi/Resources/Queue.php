<?php

namespace Ploi\Resources;

use Ploi\Http\Response;
use Ploi\Traits\HasPagination;

class Queue extends Resource
{
    use HasPagination;
    
    private $server;
    private $site;

    public function __construct(Server $server, Site $site, int $id = null)
    {
        parent::__construct($server->getPloi(), $id);

        $this->setServer($server);
        $this->setSite($site);

        $this->buildEndpoint();
    }

    public function buildEndpoint(): self
    {
        $this->setEndpoint($this->getServer()->getEndpoint() . '/' . $this->getServer()->getId() . '/sites/' . $this->getSite()->getId() . '/queues');

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

        return (is_null($this->getId())) 
            ? $this->page()
            : $this->getPloi()->makeAPICall($this->getEndpoint()); 
    }

    public function create(
        string $connection = 'database',
        string $queue = 'default',
        int $maximumSeconds = 60,
        int $sleep = 30,
        int $processes = 1,
        int $maximumTries = 1
    ): Response {
        // Remove the id
        $this->setId(null);

        // Set the options
        $options = [
            'body' => json_encode([
                'connection' => $connection,
                'queue' => $queue,
                'maximum_seconds' => $maximumSeconds,
                'sleep' => $sleep,
                'processes' => $processes,
                'maximum_tries' => $maximumTries
            ]),
        ];

        // Build the endpoint
        $this->buildEndpoint();

        // Make the request
        $response = $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);

        $this->setId($response->getData()->id);

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
        $this->setIdOrFail($id);

        // Build the endpoint
        $this->buildEndpoint();

        // Make the request
        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');
    }
}
