<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Resources\Incident;
use Tests\Unit\TestCase;

class StatusPageTest extends TestCase
{
    public function testListsStatusPages(): void
    {
        $this->queue();

        $this->ploi->statusPage()->get();

        $this->assertRequest('get', 'status-pages?page=1');
    }

    public function testGetsASingleStatusPage(): void
    {
        $this->queue();

        $this->ploi->statusPage(1)->get();

        $this->assertRequest('get', 'status-pages/1');
    }

    public function testExposesIncidents(): void
    {
        $this->assertInstanceOf(Incident::class, $this->ploi->statusPage(1)->incident());
    }
}
