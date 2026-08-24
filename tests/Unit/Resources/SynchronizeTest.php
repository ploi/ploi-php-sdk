<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Resources\Synchronize;
use Tests\Unit\TestCase;

class SynchronizeTest extends TestCase
{
    public function testSynchronizesServers(): void
    {
        $this->queue();

        (new Synchronize($this->ploi))->servers();

        $this->assertRequest('get', 'synchronize/servers');
    }
}
