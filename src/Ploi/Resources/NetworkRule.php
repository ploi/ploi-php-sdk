<?php

namespace Ploi\Resources;

use Ploi\Http\Response;

class NetworkRule extends Resource
{
    private $server;

    public function __construct(Server $server, int $id = null)
    {
        parent::__construct($server->getPloi(), $id);

        $this->setServer($server);

        $this->buildEndpoint();
    }

    public function buildEndpoint(): self
    {
        $this->setEndpoint($this->getServer()->getEndpoint() . '/' . $this->getServer()->getId() . '/network-rules');

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

    public function create(string $name, int $port, string $type = 'tcp', string $fromIpAddress = null, string $ruleType = 'allow'): Response
    {
        // Remove the id
        $this->setId(null);

        // Set the options
        $options = [
            'body' => json_encode([
                'name' => $name,
                'port' => $port,
                'type' => $type,
                'rule_type' => $ruleType,
                'from_ip_address' => $fromIpAddress,
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
