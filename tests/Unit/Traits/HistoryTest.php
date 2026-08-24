<?php

declare(strict_types=1);

namespace Tests\Unit\Traits;

use Ploi\Resources\Resource;
use Tests\Unit\TestCase;

class HistoryTest extends TestCase
{
    /**
     * @var Resource
     */
    private $resource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resource = $this->ploi->server();
    }

    public function testGetHistory(): void
    {
        $this->assertIsArray($this->resource->getHistory());
    }

    public function testAddHistory(): void
    {
        $newHistory = 'Adding to the history';

        $this->resource->addHistory($newHistory);

        $this->assertContains($newHistory, $this->resource->getHistory());
    }

    public function testSetHistory(): void
    {
        $newHistory = ['New History'];

        $this->resource->setHistory($newHistory);

        $this->assertCount(1, $this->resource->getHistory());
        $this->assertSame($newHistory, $this->resource->getHistory());
    }

    public function testSettingAnIdIsRecorded(): void
    {
        $this->resource->setHistory([]);
        $this->resource->setId(9);

        $this->assertContains('Resource ID set to 9', $this->resource->getHistory());
    }
}
