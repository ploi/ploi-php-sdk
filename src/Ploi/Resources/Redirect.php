<?php

namespace Ploi\Resources;

use Ploi\Http\Response;
use Ploi\Traits\HasPagination;

class Redirect extends Resource
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
        $this->setEndpoint($this->getServer()->getEndpoint() . '/' . $this->getServer()->getId() . '/sites/' . $this->getSite()->getId() . '/redirects');

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

    public function create(string $redirectFrom, string $redirectTo, $type = 'redirect'): Response
    {
        // Remove the id
        $this->setId(null);

        // Set the options
        $options = [
            'body' => json_encode([
                'redirect_from' => $redirectFrom,
                'redirect_to' => $redirectTo,
                'type' => $type,
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
        $this->setIdOrFail($id);

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');
    }
}
