<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Tests\Unit\TestCase;

class EnvironmentTest extends TestCase
{
    public function testGetsTheEnvironmentFile(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->environment()->get();

        $this->assertRequest('get', 'servers/1/sites/2/env');
    }

    public function testUpdatesTheEnvironmentFile(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->environment()->update('APP_ENV=production');

        $this->assertRequest('patch', 'servers/1/sites/2/env', ['content' => 'APP_ENV=production']);
    }
}
