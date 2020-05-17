<?php

namespace Ploi\Resources;

use Ploi\Ploi;
use Ploi\Traits\HasHistory;

/**
 * Class Resource
 *
 * @package Ploi\Resource
 */
class Resource
{
    use HasHistory;

    /**
     * Ploi Client
     *
     * @var Ploi
     */
    private $ploi;

    /**
     * The API endpoint for the resource
     *
     * @var null|string
     */
    private $endpoint;

    /**
     * The ID of the resource
     *
     * @var integer|null
     */
    private $id;

    /**
     * Resource constructor.
     *
     * @param Ploi|null $ploi
     * @param int|null  $id
     */
    public function __construct(Ploi $ploi = null, int $id = null)
    {
        // Set the Ploi instance if it was passed
        if ($ploi) {
            $this->setPloi($ploi);
        }

        // Set the ID of the resource if it was passed
        if ($id) {
            $this->setId($id);
        }
    }

    /**
     * Sets the ID of the resource
     *
     * @param int|null $id
     * @return self
     */
    public function setId(int $id = null): self
    {
        $this->id = $id;

        $this->addHistory("Resource ID set to " . $id);

        return $this;
    }

    /**
     * Returns the ID of the resource
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the Ploi instance
     *
     * @param Ploi $ploi
     * @return self
     */
    public function setPloi(Ploi $ploi): self
    {
        $this->ploi = $ploi;

        $this->addHistory("Ploi instance set to " . json_encode($ploi));

        return $this;
    }

    /**
     * Returns the Ploi instance
     *
     * @return Ploi
     */
    public function getPloi(): ?Ploi
    {
        return $this->ploi;
    }

    /**
     * Sets the endpoint
     *
     * @param string $endpoint
     * @return self
     */
    public function setEndpoint(string $endpoint): self
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * Returns the endpoint
     *
     * @return null|string
     */
    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }
}
