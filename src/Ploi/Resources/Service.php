<?php


namespace Ploi\Resources;

use Ploi\Http\Response;
use Ploi\Exceptions\Resource\Server\Service\RequiresServiceName;

class Service extends Resource
{
    private $server;
    private $serviceName;

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
        $this->serviceName = $serviceName;

        $this->addHistory('Resource service name set to ' . $serviceName);

        return $this;
    }

    public function restart(string $serviceName = null): Response
    {

        if ($serviceName) {
            $this->setServiceName($serviceName);
        }

        if (!$this->getServiceName()) {
            throw new RequiresServiceName;
        }

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/restart', 'post');
    }

}