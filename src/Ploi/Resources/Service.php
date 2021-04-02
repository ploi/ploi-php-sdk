<?php


namespace Ploi\Resources;

use stdClass;
use Ploi\Exceptions\Resource\Server\Service\InvalidServiceName;
use Ploi\Exceptions\Resource\Server\Service\RequiresServiceName;

class Service extends Resource
{
    private $server;
    private $serviceName;
    private $availableServices = [
        'mysql',
        'nginx',
        'supervisor',
    ];

    public function __construct(Server $server, string $serviceName = null)
    {
        parent::__construct($server->getPloi());

        $this->setServer($server);

        if ($serviceName) {
            $this->setServiceName($serviceName);
        }

        $this->buildEndpoint();
    }

    public function buildEndpoint(): self
    {
        $this->setEndpoint($this->getServer()->getEndpoint() . '/' . $this->getServer()->getId() . '/services');

        if ($this->getServiceName()) {
            $this->setEndpoint($this->getEndpoint() . '/' . $this->getServiceName());
        }

        return $this;
    }

    public function getServiceName(): ?string
    {
        return $this->serviceName;
    }

    public function setServiceName(string $serviceName): self
    {
        if (!in_array($serviceName, $this->availableServices)) {
            throw new InvalidServiceName;
        }

        $this->serviceName = $serviceName;

        $this->addHistory('Resource service name set to ' . $serviceName);

        return $this;
    }

    public function restart(string $serviceName = null): stdClass
    {

        if ($serviceName) {
            $this->setServiceName($serviceName);
        }

        if (!$this->getServiceName()) {
            throw new RequiresServiceName;
        }

        $this->buildEndpoint();

        $response = $this->getPloi()->makeAPICall($this->getEndpoint() . '/restart', 'post');

        return $response->getJson();
    }

}