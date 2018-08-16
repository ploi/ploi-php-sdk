<?php

namespace Ploi\Traits;

/**
 * Trait HasHistory
 *
 * @package Ploi\Traits
 */
trait HasHistory
{
    /**
     * @var array
     */
    private $history;

    /**
     * Returns the history of a resource
     *
     * @return array
     */
    public function getHistory(): array
    {
        return $this->history;
    }

    /**
     * Allows the history to be overridden by setting a
     * new history.
     *
     * @param array $history
     * @return self
     */
    public function setHistory(array $history): self
    {
        $this->history = $history;

        return $this;
    }

    /**
     * Adds an item to the history
     *
     * @param $history
     * @return $this
     */
    public function addHistory($history): self
    {
        $this->history[] = $history;

        return $this;
    }
}
