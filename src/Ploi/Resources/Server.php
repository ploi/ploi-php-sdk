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

    public function buildEndpoint(string $path = null): string
    {
        if (!$this->getId()) {
            return 'servers';
        }

        $base = "servers/{$this->getId()}";

        if (!$path) {
            return $base;
        }

        if (strpos($path, '/') === 0) {
            return $base . $path;
        }

        return "{$base}/{$path}";
    }

    public function callApi(string $path = null, string $method = 'get', array $options = [])
    {
        return $this->getPloi()
            ->makeAPICall($this->buildEndpoint($path), $method, $options);
    }

    public function get(int $id = null)
    {
        if ($id) {
            $this->setId($id);
        }

        return $this->callApi();
    }

    public function delete(int $id = null)
    {
        if ($id) {
            $this->setId($id);
        }

        return $this->callApi(null, 'delete');
    }

    public function logs(int $id = null)
    {
        $this->setIdOrFail($id);

        return $this->callApi('logs')
            ->getJson();
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
            $response = $this->callApi(null, 'post', $options);
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
        $this->setId(null);
        $defaults = [
            'ip' => $ip,
            'type' => 'server',
            'database_type' => 'mysql',
            'php_version' => '7.4',
        ];

        $options = [
            'body' => json_encode(array_merge($defaults, $options)),
        ];

        $response = $this->callApi('custom', 'post', $options);

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

        $endpoint = $url ?: "servers/custom/{$id}/start";

        return $this->getPloi()
            ->makeAPICall($endpoint, 'post')
            ->getJson();
    }

    public function refreshOpcache(int $id = null): stdClass
    {
        $this->setIdOrFail($id);

        return $this->callApi('refresh-opcache', 'post')
            ->getJson();
    }

    public function enableOpcache(int $id = null): stdClass
    {
        $this->setIdOrFail($id);

        return $this->callApi('enable-opcache', 'post')
            ->getJson();
    }

    public function disableOpcache(int $id = null): stdClass
    {
        $this->setIdOrFail($id);

        return $this->callApi('disable-opcache', 'delete')
            ->getJson();
    }

    public function phpVersions(int $id = null): stdClass
    {
        $this->setIdOrFail($id);

        return $this->callApi('php/versions')
            ->getJson();
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
