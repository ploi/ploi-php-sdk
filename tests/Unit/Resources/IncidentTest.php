<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Tests\Unit\TestCase;

class IncidentTest extends TestCase
{
    public function testListsIncidents(): void
    {
        $this->queue();

        $this->ploi->statusPage(1)->incident()->get();

        $this->assertRequest('get', 'status-pages/1/incidents?page=1');
    }

    public function testGetsASingleIncident(): void
    {
        $this->queue();

        $this->ploi->statusPage(1)->incident(2)->get();

        $this->assertRequest('get', 'status-pages/1/incidents/2');
    }

    public function testCreatesAnIncident(): void
    {
        $this->queue();

        $this->ploi->statusPage(1)->incident()->create('Database down', 'Investigating', 'critical');

        $this->assertRequest('post', 'status-pages/1/incidents', [
            'title'       => 'Database down',
            'description' => 'Investigating',
            'severity'    => 'critical',
        ]);
    }

    public function testDeletesAnIncident(): void
    {
        $this->queue();

        $this->ploi->statusPage(1)->incident(2)->delete();

        $this->assertRequest('delete', 'status-pages/1/incidents/2');
    }

    public function testDeleteRequiresAnId(): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->statusPage(1)->incident()->delete();
    }
}
