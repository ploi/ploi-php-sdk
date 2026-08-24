<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Tests\Unit\TestCase;

class UserTest extends TestCase
{
    public function testGetsTheUser(): void
    {
        $this->queue();

        $this->ploi->user()->get();

        $this->assertRequest('get', 'user');
    }

    public function testGetsUserStatistics(): void
    {
        $this->queue();

        $this->ploi->user()->statistics();

        $this->assertRequest('get', 'user/statistics');
    }

    public function testListsServerProviders(): void
    {
        $this->queue();

        $this->ploi->user()->serverProviders();

        $this->assertRequest('get', 'user/server-providers');
    }

    public function testGetsASingleServerProvider(): void
    {
        $this->queue();

        $this->ploi->user()->serverProviders(3);

        $this->assertRequest('get', 'user/server-providers/3');
    }
}
