<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Tests\Unit\TestCase;

class NginxConfigurationTest extends TestCase
{
    public function testGetsTheNginxConfiguration(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->nginxConfiguration()->get();

        $this->assertRequest('get', 'servers/1/sites/2/nginx-configuration');
    }

    public function testUpdatesTheNginxConfiguration(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->nginxConfiguration()->update('server { listen 80; }');

        $this->assertRequest('patch', 'servers/1/sites/2/nginx-configuration', [
            'content' => 'server { listen 80; }',
        ]);
    }
}
