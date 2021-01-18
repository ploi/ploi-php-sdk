<?php

namespace Ploi\Resources;

use stdClass;
use Ploi\Exceptions\Http\NotValid;

class Database extends Resource
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
        $this->setEndpoint($this->getServer()->getEndpoint() . '/' . $this->getServer()->getId() . '/databases');

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

    public function create(string $name, string $user, string $password): stdClass
    {
        // Remove the id
        $this->setId(null);

        // Set the options
        $options = [
            'body' => json_encode([
                'name' => $name,
                'user' => $user,
                'password' => $password,
            ]),
        ];

        // Build the endpoint
        $this->buildEndpoint();

        // Make the request
        try {
            $response = $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);
        } catch (NotValid $exception) {
            return json_decode($exception->getMessage());
        }

        // Set the id of the site
        $this->setId($response->getJson()->data->id);

        // Return the data
        return $response->getJson()->data;
    }

    public function delete(int $id): bool
    {
        if ($id) {
            $this->setId($id);
        }

        $this->buildEndpoint();

        $response = $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');

        return $response->getResponse()->getStatusCode() === 200;
    }

    public function acknowledge(string $name): stdClass
    {
        // Remove the id
        $this->setId(null);

        // Set the action
        $this->setAction('acknowledge');

        // Set the options
        $options = [
            'body' => json_encode([
                'name' => $name,
            ]),
        ];

        // Build the endpoint
        $this->buildEndpoint();

        // Make the request
        try {
            $response = $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);
        } catch (NotValid $exception) {
            return json_decode($exception->getMessage());
        }

        // Set the id of the site
        //$this->setId($response->getJson()->data->id);

        // Return the data
        return $response->getJson()->data;
    }

    public function forget(int $id): bool
    {
        if ($id) {
            $this->setId($id);
        }

        // Set the action
        $this->setAction('forget');

        $this->buildEndpoint();

        $response = $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');

        return $response->getResponse()->getStatusCode() === 200;
    }

    public function createBackup(
        int $id,
        int $interval,
        string $type,
        string $table_exclusions = null,
        string $locations = null,
        string $path = null
    ): stdClass
    {

        if ($id) {
            $this->setId($id);
        }


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

        $this->setAction('backups');
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

        // Return the data
        return $response->getData();
    }

    public function getBackups(int $id = null)
    {
        if ($id) {
            $this->setId($id);
        }

        $this->setAction('backups');
        $this->buildEndpoint();

        $response = $this->getPloi()->makeAPICall($this->getEndpoint());
        return $response->getData();
    }
}
