<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Tests\Unit\TestCase;

class FastCgiTest extends TestCase
{
    public function testEnablesFastCgiCache(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->fastCgi()->enable();

        $this->assertRequest('post', 'servers/1/sites/2/fastcgi-cache/enable');
    }

    public function testDisablesFastCgiCache(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->fastCgi()->disable();

        $this->assertRequest('delete', 'servers/1/sites/2/fastcgi-cache/disable');
    }

    public function testFlushesFastCgiCache(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->fastCgi()->flush();

        $this->assertRequest('post', 'servers/1/sites/2/fastcgi-cache/flush');
    }
}
