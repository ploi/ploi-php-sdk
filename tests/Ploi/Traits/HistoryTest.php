<?php

namespace Tests\Ploi\Traits;

use Tests\BaseTest;
use Ploi\Resources\Resource;

/**
 * Class HistoryTest
 *
 * @package Tests\Ploi\Traits
 */
class HistoryTest extends BaseTest
{
    /**
     * @var Resource
     */
    private $resource;

    public function setup(): void
    {
        parent::setup();

        $this->resource = $this->getPloi()->server();
    }

    public function testGetHistory()
    {
        $this->assertIsArray($this->resource->getHistory());
    }

    public function testAddHistory()
    {
        $newHistory ='Adding to the history';
        $this->resource->addHistory($newHistory);

        $this->assertContains($newHistory, $this->resource->getHistory());
    }

    public function testSetHistory()
    {
        $newHistory = [
            'New History'
        ];

        // Set the history
        $this->resource->setHistory($newHistory);

        $this->assertEquals(1, count($this->resource->getHistory()));
        $this->assertEquals($newHistory, $this->resource->getHistory());
    }
}
