<?php

namespace Ploi\Traits;

use Ploi\Http\Response;
use Ploi\Ploi;

/**
 * Trait HasSearch
 *
 * Provides search functionality for API calls.
 *
 * @package Ploi\Traits
 */
trait HasSearch
{
    /**
     * @var string|null
     */
    protected $searchQuery;

    /**
     * Get the Ploi instance.
     *
     * @return Ploi|null
     */
    abstract public function getPloi(): ?Ploi;

    /**
     * Get the API endpoint.
     *
     * @return string|null
     */
    abstract public function getEndpoint(): ?string;

    /**
     * Set the search query.
     *
     * @param string|null $searchQuery
     * @return self
     */
    public function setSearchQuery(?string $searchQuery = null): self
    {
        $this->searchQuery = $searchQuery;

        return $this;
    }

    /**
     * Perform a search with the given query.
     *
     * @param string $searchQuery
     * @return Response
     * @throws \Exception
     */
    public function search(string $searchQuery): Response
    {
        $ploi = $this->getPloi();
        $endpoint = $this->getEndpoint();

        if (is_null($ploi) || is_null($endpoint)) {
            throw new \Exception('Ploi instance or endpoint is not set.');
        }

        return $ploi->makeAPICall(
            $endpoint . $this->buildSearchQuery($searchQuery)
        );
    }

    /**
     * Build the search query string.
     *
     * @param string $searchQuery
     * @return string
     */
    protected function buildSearchQuery(string $searchQuery): string
    {
        return "?search={$searchQuery}";
    }
}
