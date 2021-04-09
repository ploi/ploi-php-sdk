<?php

namespace Ploi\Resources;

use stdClass;
use Ploi\Ploi;
use Ploi\Exceptions\Http\NotValid;
use Ploi\Exceptions\Resource\RequiresId;

class Server extends Resource
{
    private $endpoint = 'servers';

    public function __construct(Ploi $ploi = null, int $id = null)
    {
        parent::__construct($ploi, $id);

        $this->setEndpoint($this->endpoint);
    }

    public function get(int $id = null)
    {
        if ($id) {
            $this->setId($id);
        }

        if ($this->getId()) {
            $this->setEndpoint($this->endpoint . '/' . $this->getId());
        }

        return $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    public function delete(int $id = null)
    {
        if ($id) {
            $this->setId($id);
        }

        if ($this->getId()) {
            $this->setEndpoint($this->endpoint . '/' . $this->getId());
        }

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');
    }

    public function logs(int $id = null)
    {
        $this->setIdOrFail($id);

        $this->setEndpoint($this->endpoint . '/' . $this->getId() . '/logs');

        return $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    public function create(
        string $name,
        int $provider,
        $region,
        $plan,
        array $options = []
    ): stdClass {

        // Remove the id
        $this->setId(null);

        $defaults = [
            'name' => $name,
            'plan' => $plan,
            'region' => $region,
            'credential' => $provider,
            'type' => 'server',
            'database_type' => 'mysql',
            'webserver_type' => 'nginx',
            'php_version' => '7.4'
        ];

        // Set the options
        $options = [
            'body' => json_encode(array_merge($defaults, $options)),
        ];

        // Make the request
        try {
            $response = $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);
        } catch (NotValid $exception) {
            // $errors = json_decode($exception->getMessage())->errors;
            // dd($errors);

            throw $exception;
        }

        // Set the id of the site
        $this->setId($response->getJson()->data->id);

        // Return the data
        return $response->getData();
    }

    public function createCustom(string $ip, array $options): stdClass
    {
        $endpoint = $this->getEndpoint() . '/custom';

        $defaults = [
            'ip' => $ip,
            'type' => 'server',
            'database_type' => 'mysql',
            'php_version' => '7.4',
        ];

        $options = [
            'body' => json_encode(array_merge($defaults, $options)),
        ];

        $response = $this->getPloi()->makeAPICall($endpoint, 'post', $options);

        $data = $response->getJson();
        $this->setId($data->id);

        return $data;
    }

    public function startInstallation(string $url = null)
    {
        $id = $this->getId();

        if (!$url && !$id) {
            throw new RequiresId("This endpoint requires an ID. Supply an ID or a valid installation url.");
        }

        $endpoint = $url ?: $this->getEndpoint() . "/custom/{$id}/start";

        $response = $this->getPloi()->makeAPICall($endpoint, 'post');

        $data = $response->getJson();

        return $data;
    }

    public function sshKeys(int $id = null): array
    {
        $this->setIdOrFail($id);

        $this->setEndpoint($this->endpoint . '/' . $this->getId());

        $response = $this->getPloi()->makeAPICall($this->getEndpoint() . '/ssh-keys');

        return $response->getData();
    }

    public function refreshOpcache(int $id = null): stdClass
    {
        $this->setIdOrFail($id);

        $this->setEndpoint($this->endpoint . '/' . $this->getId());

        $response = $this->getPloi()->makeAPICall($this->getEndpoint() . '/refresh-opcache', 'post');

        return $response->getJson();
    }

    public function enableOpcache(int $id = null): stdClass
    {
        $this->setIdOrFail($id);

        $this->setEndpoint($this->endpoint . '/' . $this->getId());

        $response = $this->getPloi()->makeAPICall($this->getEndpoint() . '/enable-opcache', 'post');

        return $response->getJson();
    }

    public function disableOpcache(int $id = null): stdClass
    {
        $this->setIdOrFail($id);

        $this->setEndpoint($this->endpoint . '/' . $this->getId());

        $response = $this->getPloi()->makeAPICall($this->getEndpoint() . '/disable-opcache', 'delete');

        return $response->getJson();
    }

    public function phpVersions(int $id = null): stdClass
    {
        $this->setIdOrFail($id);

        $this->setEndpoint($this->endpoint . '/' . $this->getId());

        $response = $this->getPloi()->makeAPICall($this->getEndpoint() . '/php/versions', 'get');

        return $response->getJson();
    }

    public function sites($id = null): Site
    {
        return new Site($this, $id);
    }

    public function services(string $serviceName = null): Service
    {
        return new Service($this, $serviceName);
    }

    public function databases($id = null): Database
    {
        return new Database($this, $id);
    }
    public function cronjobs($id = null): Cronjob
    {
        return new Cronjob($this, $id);
    }

    public function networkRules($id = null): NetworkRule
    {
        return new NetworkRule($this, $id);
    }

    public function systemUsers($id = null): SystemUser
    {
        return new SystemUser($this, $id);
    }

    public function daemons($id = null): Daemon
    {
        return new Daemon($this, $id);
    }
}
