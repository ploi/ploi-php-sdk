<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Tests\Unit\TestCase;

class RobotTest extends TestCase
{
    public function testAllowsRobots(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->robots()->allow();

        $this->assertRequest('patch', 'servers/1/sites/2', ['disable_robots' => false]);
    }

    public function testBlocksRobots(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->robots()->block();

        $this->assertRequest('patch', 'servers/1/sites/2', ['disable_robots' => true]);
    }
}
