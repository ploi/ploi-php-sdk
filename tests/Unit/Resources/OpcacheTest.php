<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Tests\Unit\TestCase;

class OpcacheTest extends TestCase
{
    public function testRefreshesOpcache(): void
    {
        $this->queue();

        $this->ploi->servers(1)->opcache()->refresh();

        $this->assertRequest('post', 'servers/1/refresh-opcache');
    }

    public function testEnablesOpcache(): void
    {
        $this->queue();

        $this->ploi->servers(1)->opcache()->enable();

        $this->assertRequest('post', 'servers/1/enable-opcache');
    }

    /**
     * Note this posts, where the deprecated Server::disableOpcache() deletes.
     */
    public function testDisablesOpcache(): void
    {
        $this->queue();

        $this->ploi->servers(1)->opcache()->disable();

        $this->assertRequest('post', 'servers/1/disable-opcache');
    }
}
