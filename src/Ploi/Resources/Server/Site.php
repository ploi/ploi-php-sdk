<?php

namespace Ploi\Resources\Server;

use Ploi\Exceptions\Http\InternalServerError;
use Ploi\Exceptions\Http\NotFound;
use Ploi\Exceptions\Http\NotValid;
use Ploi\Exceptions\Http\PerformingMaintenance;
use Ploi\Exceptions\Http\TooManyAttempts;
use Ploi\Exceptions\Resource\RequiresId;
use Ploi\Exceptions\Resource\Server\Site\DomainAlreadyExists;
use Ploi\Resources\Resource;
use stdClass;

/**
 * Class Site
 *
 * @package Ploi\Resources\Server
 */
class Site extends Resource
{
    /**
     * @var Server
     */
    private $server;

    /**
     * Site constructor.
     *
     * @param Server $server
     * @param int|null $id
     */
    public function __construct(Server $server, int $id = null)
    {
        parent::__construct($server->getPloi(), $id);

        $this->setServer($server);

        // Build the endpoint
        $this->buildEndpoint();
    }

    /**
     * Returns either a single site or an array of sites
     *
     * @param int|null $id
     * @return null|\Ploi\Http\Response
     * @throws \Ploi\Exceptions\Http\InternalServerError
     * @throws \Ploi\Exceptions\Http\NotFound
     * @throws \Ploi\Exceptions\Http\NotValid
     * @throws \Ploi\Exceptions\Http\PerformingMaintenance
     * @throws \Ploi\Exceptions\Http\TooManyAttempts
     */
    public function get(int $id = null)
    {
        if ($id) {
            $this->setId($id);
        }

        // Make sure the endpoint is built
        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    /**
     * Gets the server
     *
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * Sets the server
     *
     * @param Server $server
     * @return Site
     */
    public function setServer(Server $server): self
    {
        $this->server = $server;

        return $this;
    }

    /**
     * Builds the endpoint out based over the server's endpoint and id
     *
     * @return Site
     */
    public function buildEndpoint(): self
    {
        $this->setEndpoint($this->getServer()->getEndpoint() . '/' . $this->getServer()->getId() . '/sites');

        if ($this->getId()) {
            $this->setEndpoint($this->getEndpoint() . '/' . $this->getId());
        }

        return $this;
    }

    /**
     * Creates a new website
     *
     * @param string $domain
     * @param string $webDirectory
     * @param string $projectRoot
     * @return \Exception|NotValid
     * @throws DomainAlreadyExists
     * @throws InternalServerError
     * @throws NotFound
     * @throws PerformingMaintenance
     * @throws TooManyAttempts
     */
    public function create(string $domain, string $webDirectory = '/public', string $projectRoot = '/'): stdClass
    {
        // Remove the id
        $this->setId(null);

        // Set the options
        $options = [
            'body' => json_encode([
                'root_domain' => $domain,
                'web_directory' => $webDirectory,
                'project_root' => $projectRoot,
            ]),
        ];

        // Build the endpoint
        $this->buildEndpoint();

        // Make the request
        try {
            $response = $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);
        } catch (NotValid $exception) {
            $errors = json_decode($exception->getMessage())->errors;

            if (!empty($errors->root_domain)
                && $errors->root_domain[0] === "The root domain has already been taken.") {
                throw new DomainAlreadyExists($domain . ' already exists!');
            }

            return $exception;
        }

        // Set the id of the site
        $this->setId($response->getJson()->data->id);

        // Return the data
        return $response->getJson()->data;
    }

    /**
     * Deletes a site
     *
     * @param int|null $id
     * @return stdClass
     * @throws InternalServerError
     * @throws NotFound
     * @throws NotValid
     * @throws PerformingMaintenance
     * @throws TooManyAttempts
     * @throws \Exception
     */
    public function delete(int $id = null): ?bool
    {
        if ($id) {
            $this->setId($id);
        }

        $this->buildEndpoint();

        $response = $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');

        if ($response->getResponse()->getStatusCode() === 200) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns the logs as an array for a site
     *
     * @param int|null $id
     * @return array
     * @throws InternalServerError
     * @throws NotFound
     * @throws NotValid
     * @throws PerformingMaintenance
     * @throws RequiresId
     * @throws TooManyAttempts
     */
    public function logs(int $id = null): array
    {
        if ($id) {
            $this->setId($id);
        }

        if (!$this->getId()) {
            throw new RequiresId("No Site ID set");
        }

        $this->setEndpoint($this->buildEndpoint()->getEndpoint() . '/log');

        $response = $this->getPloi()->makeAPICall($this->getEndpoint());

        // Wrap the logs if they're not already wrapped
        if (!is_array($response->getJson()->data)) {
            return [$response->getJson()->data];
        }

        return $response->getJson()->data;
    }
}
