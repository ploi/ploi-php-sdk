<?php


namespace Ploi\Resources;

use Ploi\Http\Response;
use Ploi\Traits\HasPagination;

class DatabaseBackup extends Resource
{
    use HasPagination;

    public function __construct(Server $server, Database $database, ?int $id = null)
    {
        parent::__construct($server->getPloi(), $id);

        $this->setServer($server);

        $this->setDatabase($database);

        $this->buildEndpoint();
    }

    public function buildEndpoint(): self
    {
        $this->setEndpoint($this->getServer()->getEndpoint() . '/' . $this->getServer()->getId() . '/databases/' . $this->getDatabase()->getId() . '/backups');

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

    public function create(
        int $interval,
        string $type,
        ?string $table_exclusions = null,
        ?string $locations = null,
        ?string $path = null
    ): Response
    {
        // Remove the id
        $this->setId(null);

        // Set the options
        $options = [
            'body' => json_encode([
                'interval' => $interval,
                'type' => $type,
                'table_exclusions' => $table_exclusions,
                'locations' => $locations,
                'path' => $path
            ]),
        ];

        $this->buildEndpoint();

        // Make the request
        $response = $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);

        $this->setId($response->getJson()->data->id);

        // Return the response
        return $response;
    }

    public function delete(?int $id = null): Response
    {
        $this->setIdOrFail($id);

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');
    }

    public function toggle(?int $id = null): Response
    {
        $this->setIdOrFail($id);

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/toggle', 'patch');
    }
}
