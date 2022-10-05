<?php

namespace Ploi\Resources;

use Ploi\Ploi;
use Ploi\Http\Response;
use Ploi\Traits\HasPagination;

class Project extends Resource
{
    use HasPagination;

    private $endpoint = 'projects';

    public function __construct(Ploi $ploi = null, int $id = null)
    {
        parent::__construct($ploi, $id);

        $this->setEndpoint($this->endpoint);
    }

    public function buildEndpoint(string $path = null): string
    {
        $base = $this->endpoint;

        if ($this->getId()) {
            $base = "{$base}/{$this->getId()}";
        }

        if (!$path) {
            return $base;
        }

        if (strpos($path, '/') === 0) {
            return $base . $path;
        }

        return "{$base}/{$path}";
    }

    public function callApi(string $path = null, string $method = 'get', array $options = []): Response
    {
        return $this->getPloi()
            ->makeAPICall($this->buildEndpoint($path), $method, $options);
    }

    public function get(int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        // This method do not need the special callApi() method on pagination
        // Since its a the simple get of the servers using the $this->endpoint url

        return (is_null($this->getId()))
            ? $this->page()
            : $this->callApi();
    }

    public function delete(int $id = null): Response
    {
        $this->setIdOrFail($id);

        return $this->callApi(null, 'delete');
    }

    public function create(
        string $title,
        array $servers = [],
        array $sites = [],
        array $options = []
    ): Response {

        // Remove the id
        $this->setId(null);

        $defaults = [
            'title' => $title,
            'servers' => $servers,
            'sites' => $sites,
        ];

        // Set the options
        $options = [
            'body' => json_encode(array_merge($defaults, $options)),
        ];

        // Make the request
        $response = $this->callApi(null, 'post', $options);

        // Set the id of the site
        $this->setId($response->getJson()->data->id);

        // Return the response
        return $response;
    }

    public function update(string $title, array $servers = [], array $sites = []): Response
    {
        $this->setIdOrFail();

        $options = [
            'body' => json_encode([
                'title' => $title,
                'servers' => $servers,
                'sites' => $sites,
            ]),
        ];

        $this->buildEndpoint();

        return $this->callApi(null, 'patch', $options);
    }
}
