<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\Server\Service\RequiresServiceName;
use Tests\Unit\TestCase;

class ServiceTest extends TestCase
{
    public function testRestartsAServiceGivenAtConstruction(): void
    {
        $this->queue();

        $this->ploi->servers(1)->services('nginx')->restart();

        $this->assertRequest('post', 'servers/1/services/nginx/restart');
    }

    public function testRestartsAServiceGivenAtCallTime(): void
    {
        $this->queue();

        $this->ploi->servers(1)->services()->restart('mysql');

        $this->assertRequest('post', 'servers/1/services/mysql/restart');
    }

    public function testRestartRequiresAServiceName(): void
    {
        $this->expectException(RequiresServiceName::class);

        $this->ploi->servers(1)->services()->restart();
    }

    public function testRecordsTheServiceNameInTheHistory(): void
    {
        $service = $this->ploi->servers(1)->services('nginx');

        $this->assertContains('Resource service name set to nginx', $service->getHistory());
        $this->assertSame('nginx', $service->getServiceName());
    }
}
