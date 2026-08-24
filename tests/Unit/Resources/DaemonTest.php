<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Tests\Unit\TestCase;

class DaemonTest extends TestCase
{
    public function testListsDaemons(): void
    {
        $this->queue();

        $this->ploi->servers(1)->daemons()->get();

        $this->assertRequest('get', 'servers/1/daemons?page=1');
    }

    public function testGetsASingleDaemon(): void
    {
        $this->queue();

        $this->ploi->servers(1)->daemons(2)->get();

        $this->assertRequest('get', 'servers/1/daemons/2');
    }

    public function testCreatesADaemon(): void
    {
        $this->queue(['data' => ['id' => 2]]);

        $daemon = $this->ploi->servers(1)->daemons();
        $daemon->create('php artisan horizon', 'ploi', 2);

        $this->assertRequest('post', 'servers/1/daemons', [
            'command'     => 'php artisan horizon',
            'system_user' => 'ploi',
            'processes'   => 2,
            'directory'   => null,
        ]);

        $this->assertSame(2, $daemon->getId());
    }

    public function testCreatesADaemonInADirectory(): void
    {
        $this->queue(['data' => ['id' => 2]]);

        $this->ploi->servers(1)->daemons()->create('php artisan horizon', 'deployer', 1, '/home/deployer/app');

        $this->assertRequest('post', 'servers/1/daemons', [
            'command'     => 'php artisan horizon',
            'system_user' => 'deployer',
            'processes'   => 1,
            'directory'   => '/home/deployer/app',
        ]);
    }

    public function testRestartsADaemon(): void
    {
        $this->queue();

        $this->ploi->servers(1)->daemons(2)->restart();

        $this->assertRequest('post', 'servers/1/daemons/2/restart');
    }

    public function testPausesADaemon(): void
    {
        $this->queue();

        $this->ploi->servers(1)->daemons(2)->pause();

        $this->assertRequest('post', 'servers/1/daemons/2/toggle-pause');
    }

    public function testDeletesADaemon(): void
    {
        $this->queue();

        $this->ploi->servers(1)->daemons(2)->delete();

        $this->assertRequest('delete', 'servers/1/daemons/2');
    }

    /**
     * @return array<string, array{0: string}>
     */
    public function requiresIdProvider(): array
    {
        return [
            'restart' => ['restart'],
            'pause'   => ['pause'],
            'delete'  => ['delete'],
        ];
    }

    /**
     * @dataProvider requiresIdProvider
     */
    public function testMethodsThatNeedADaemonIdThrowWithoutOne(string $method): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->servers(1)->daemons()->{$method}();
    }
}
