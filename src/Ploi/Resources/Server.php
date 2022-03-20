<?php

namespace Ploi\Resources;

use Ploi\Ploi;
use Ploi\Http\Response;
use Ploi\Traits\HasPagination;
use Ploi\Exceptions\Resource\RequiresId;

class Server extends Resource
{
    use HasPagination;

    private $endpoint = 'servers';

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

    public function logs(int $id = null): Response
    {
        $this->setIdOrFail($id);

        return $this->callApi('logs');
    }

    public function create(
        string $name,
        int $provider,
        $region,
        $plan,
        array $options = []
    ): Response {

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
        $response = $this->callApi(null, 'post', $options);

        // Set the id of the site
        $this->setId($response->getJson()->data->id);

        // Return the response
        return $response;
    }

    public function createCustom(string $ip, array $options): Response
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

        $this->setId($response->getJson()->id);

        return $response;
    }

    public function startInstallation(string $url = null): Response
    {
        $id = $this->getId();

        if (!$url && !$id) {
            throw new RequiresId("This endpoint requires an ID. Supply an ID or a valid installation url.");
        }

        $endpoint = $url ?: "servers/custom/{$id}/start";

        return $this->getPloi()
            ->makeAPICall($endpoint, 'post');
    }

    /**
     * @deprecated Will be removed in future versions, use server()->opcache()->refresh() instead.
     */
    public function refreshOpcache(int $id = null): Response
    {
        $this->setIdOrFail($id);

        return $this->callApi('refresh-opcache', 'post');
    }

    /**
     * @deprecated Will be removed in future versions, use server()->opcache()->enable() instead.
     */
    public function enableOpcache(int $id = null): Response
    {
        $this->setIdOrFail($id);

        return $this->callApi('enable-opcache', 'post');
    }

    /**
     * @deprecated Will be removed in future versions, use server()->opcache()->disable() instead.
     */
    public function disableOpcache(int $id = null): Response
    {
        $this->setIdOrFail($id);

        return $this->callApi('disable-opcache', 'delete');
    }

    public function phpVersions(int $id = null): Response
    {
        $this->setIdOrFail($id);

        return $this->callApi('php/versions');
    }

    public function restart(int $id = null): Response
    {
        $this->setIdOrFail($id);

        return $this->callApi('restart', 'post');
    }

    public function monitoring(int $id = null): Response
    {
        $this->setIdOrFail($id);

        return $this->callApi('monitor');
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

    public function sshKeys($id = null): SshKey
    {
        return new SshKey($this, $id);
    }

    public function opcache($id = null): Opcache
    {
        return new Opcache($this, $id);
    }

    public function insights($id = null): Insight
    {
        return new Insight($this, $id);
    }
}
