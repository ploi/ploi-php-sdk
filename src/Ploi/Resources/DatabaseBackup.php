<?php


namespace Ploi\Resources;

use stdClass;
use Ploi\Exceptions\Http\NotValid;

class DatabaseBackup extends Resource
{
    private $server;
    private $database;

    public function __construct(Server $server, Database $database, int $id = null)
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

    public function get(int $id = null)
    {
        if ($id) {
            $this->setId($id);
        }

        // Make sure the endpoint is built
        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    public function create(
        int $interval,
        string $type,
        string $table_exclusions = null,
        string $locations = null,
        string $path = null
    ): stdClass
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
        try {
            $response = $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);
        } catch
        (NotValid $exception) {
            $errors = json_decode($exception->getMessage())->errors;

            dd($errors);

            throw $exception;
        }

        $data = $response->getData();

        $this->setId($data->id);

        // Return the data
        return $data;
    }
}
