<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Tests\Unit\TestCase;

class MonitorsTest extends TestCase
{
    public function testListsMonitors(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->monitors()->get();

        $this->assertRequest('get', 'servers/1/sites/2/monitors?page=1');
    }

    public function testGetsASingleMonitor(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->monitors(3)->get();

        $this->assertRequest('get', 'servers/1/sites/2/monitors/3');
    }

    public function testGetsUptimeResponses(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->monitors(3)->uptimeResponses();

        $this->assertRequest('get', 'servers/1/sites/2/monitors/3/uptime-responses');
    }

    public function testUptimeResponsesRequiresAnId(): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->servers(1)->sites(2)->monitors()->uptimeResponses();
    }
}
