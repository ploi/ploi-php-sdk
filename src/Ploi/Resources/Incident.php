<?php

namespace Ploi\Resources;

use Ploi\Http\Response;
use Ploi\Traits\HasPagination;

class Incident extends Resource
{
    use HasPagination;

    private $statusPage;

    public function __construct(StatusPage $statusPage, ?int $id = null)
    {
        parent::__construct($statusPage->getPloi(), $id);

        $this->setStatusPage($statusPage);

        $this->buildEndpoint();
    }

    public function setStatusPage(StatusPage $statusPage): self
    {
        $this->statusPage = $statusPage;

        return $this;
    }

    public function getStatusPage(): ?StatusPage
    {
        return $this->statusPage;
    }

    public function buildEndpoint(): self
    {
        $endpoint = $this->getStatusPage()->getEndpoint() . '/incidents';

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

    public function create(string $title, string $description, string $severity): Response
    {

        $options = [
            'body' => json_encode([
                'title' => $title,
                'description' => $description,
                'severity' => $severity,
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