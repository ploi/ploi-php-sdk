<?php

namespace Ploi\Resources\Server;

use Ploi\Exceptions\Http\InternalServerError;
use Ploi\Exceptions\Http\NotFound;
use Ploi\Exceptions\Http\NotValid;
use Ploi\Exceptions\Http\PerformingMaintenance;
use Ploi\Exceptions\Http\TooManyAttempts;
use Ploi\Exceptions\Resource\RequiresId;
use Ploi\Http\Response;
use Ploi\Ploi;
use Ploi\Resources\Resource;
use Psr\Http\Message\ResponseInterface;

class Server extends Resource
{
    /**
     * @var string
     */
    private $endpoint = 'servers';

    public function __construct(Ploi $ploi = null, int $id = null)
    {
        parent::__construct($ploi, $id);

        $this->setEndpoint($this->endpoint);
    }

    /**
     * Gets all or a specific server Id
     *
     * @param int|null $id ID of the server
     * @return Response
     * @throws InternalServerError
     * @throws NotFound
     * @throws NotValid
     * @throws PerformingMaintenance
     * @throws TooManyAttempts
     */
    public function get(int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        if ($this->getId()) {
            // Get the specific resource
            $this->setEndpoint($this->endpoint . '/' . $this->getId());
        }

        return $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    /**
     * Returns the logs for a server
     *
     * @param int|null $id ID of the server
     * @return Response
     * @throws InternalServerError
     * @throws NotFound
     * @throws NotValid
     * @throws PerformingMaintenance
     * @throws RequiresId
     * @throws TooManyAttempts
     */
    public function logs(int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        if (!$this->getId()) {
            throw new RequiresId("No server ID set");
        }

        $this->setEndpoint($this->endpoint . '/' . $this->getId() . '/logs');

        return $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    /**
     * @param null $id
     * @return Site
     */
    public function site($id = null): Site
    {
        return new Site($this, $id);
    }
}
