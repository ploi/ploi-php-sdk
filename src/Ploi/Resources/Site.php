<?php

namespace Ploi\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Ploi\Http\Response;
use Ploi\Traits\HasPagination;

/**
 * Class Site
 *
 * @package Services\Ploi\resources\Server
 */
class Site extends Resource
{
    use HasPagination;

    private $server;

    public function __construct(Server $server, ?int $id = null)
    {
        parent::__construct($server->getPloi(), $id);

        $this->setServer($server);

        // Build the endpoint
        $this->buildEndpoint();
    }

    public function get(?int $id = null)
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

    public function getServer(): Server
    {
        return $this->server;
    }

    public function setServer(Server $server)
    {
        $this->server = $server;

        return $this;
    }

    public function buildEndpoint(): self
    {
        $this->setEndpoint($this->getServer()->getEndpoint() . '/' . $this->getServer()->getId() . '/sites');

        if ($this->getId()) {
            $this->setEndpoint($this->getEndpoint() . '/' . $this->getId());
        }

        return $this;
    }

    public function create(
        string $domain,
        string $webDirectory = '/public',
        string $projectRoot = '/',
        string $systemUser = 'ploi',
        ?string $systemUserPassword = null,
        ?string $webserverTemplate = null,
        ?string $projectType = null
    ): Response {

        // Remove the id
        $this->setId(null);

        // Set the options
        $options = [
            'body' => json_encode([
                'root_domain' => $domain,
                'web_directory' => $webDirectory,
                'project_root' => $projectRoot,
                'system_user' => $systemUser,
                'system_user_password' => $systemUserPassword,
                'webserver_template' => $webserverTemplate,
                'project_type' => $projectType
            ]),
        ];

        // Build the endpoint
        $this->buildEndpoint();

        // Make the request
        $response = $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);

        // Set the id of the site
        $this->setId($response->getJson()->data->id);

        // Return the response
        return $response;
    }

    public function update(string $rootDomain): Response
    {
        $this->setIdOrFail();

        $options = [
            'body' => json_encode([
                'root_domain' => $rootDomain,
            ])
        ];

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'patch', $options);
    }

    public function delete(?int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');
    }

    public function logs(?int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        if (!$this->getId()) {
            throw new RequiresId('No Site ID set');
        }

        $this->setEndpoint($this->buildEndpoint()->getEndpoint() . '/log');

        return $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    public function phpVersion($version = '7.4'): Response
    {
        // Set the options
        $options = [
            'body' => json_encode([
                'php_version' => $version,
            ]),
        ];

        // Build the endpoint
        $this->buildEndpoint();

        // Make the request
        $response = $this->getPloi()->makeAPICall($this->getEndpoint() . '/php-version', 'post', $options);

        // Set the id of the site
        $this->setId($response->getJson()->data->id);

        // Return the data
        return $response;
    }

    public function testDomain(?int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        if (!$this->getId()) {
            throw new RequiresId('No Site ID set');
        }

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/test-domain', 'get');
    }

    public function enableTestDomain(?int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        if (!$this->getId()) {
            throw new RequiresId('No Site ID set');
        }

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/test-domain', 'post');
    }

    public function disableTestDomain(?int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        if (!$this->getId()) {
            throw new RequiresId('No Site ID set');
        }

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/test-domain', 'delete');
    }

    public function suspend(?int $id = null, ?string $reason = null): Response
    {
        $this->setIdOrFail($id);

        $options = [];
        if ($reason) {
            $options = [
                'body' => json_encode([
                    'reason' => $reason,
                ]),
            ];
        }

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/suspend', 'post', $options);
    }

    public function resume(?int $id = null): Response
    {
        $this->setIdOrFail($id);

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/resume', 'post');
    }

    public function horizonStatistics(string $type = 'stats'): Response
    {
        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/laravel/horizon/' . $type);
    }

    public function redirects($id = null): Redirect
    {
        return new Redirect($this->getServer(), $this, $id);
    }

    public function certificates($id = null): Certificate
    {
        return new Certificate($this->getServer(), $this, $id);
    }

    public function repository(): Repository
    {
        return new Repository($this->getServer(), $this);
    }

    public function queues($id = null): Queue
    {
        return new Queue($this->getServer(), $this, $id);
    }

    public function deployment(): Deployment
    {
        return new Deployment($this->getServer(), $this);
    }

    public function app($id = null): App
    {
        return new App($this->getServer(), $this, $id);
    }

    public function environment(): Environment
    {
        return new Environment($this->getServer(), $this);
    }

    public function alias(): Alias
    {
        return new Alias($this->getServer(), $this);
    }

    public function fastCgi(): FastCgi
    {
        return new FastCgi($this->getServer(), $this);
    }

    public function authUser(?int $id = null): AuthUser
    {
        return new AuthUser($this->getServer(), $this, $id);
    }

    public function robots(): Robot
    {
        return new Robot($this->getServer(), $this);
    }

    public function tenants(): Tenant
    {
        return new Tenant($this);
    }

    public function monitors(?int $id = null): Monitors
    {
        return new Monitors($this, $id);
    }

    public function nginxConfiguration(): NginxConfiguration
    {
        return new NginxConfiguration($this);
    }
}
