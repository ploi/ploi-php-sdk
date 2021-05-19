<?php


namespace Ploi\Traits;

use Ploi\Http\Response;
use Ploi\Ploi;

/**
 * Trait HasPagination
 *
 * @package Ploi\Traits
 */
trait HasPagination
{
    /**
     * @var int
     */
    protected $amountPerPage;

    abstract public function getPloi(): ?Ploi;

    abstract public function getEndpoint(): ?string;

    public function perPage(?int $amountPerPage = null): self
    {
        $this->amountPerPage = $amountPerPage;

        return $this;
    }

    public function page(int $pageNumber = 1, ?int $amountPerPage = null): Response
    {
        return $this->getPloi()->makeAPICall(
            $this->getEndpoint() . $this->getPaginationQuery($pageNumber, $amountPerPage)
        );
    }

    protected function getPaginationQuery(int $pageNumber, ?int $amountPerPage = null): string
    {
        $path = "?page={$pageNumber}";

        if ($amountPerPage) {
            $this->perPage($amountPerPage);
        }

        if ($this->amountPerPage) {
            $path .= "&per_page={$this->amountPerPage}";
        }

        return $path;
    }
}