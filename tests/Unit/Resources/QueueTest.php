<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Tests\Unit\TestCase;

class QueueTest extends TestCase
{
    public function testListsQueues(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->queues()->get();

        $this->assertRequest('get', 'servers/1/sites/2/queues?page=1');
    }

    public function testGetsASingleQueue(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->queues(3)->get();

        $this->assertRequest('get', 'servers/1/sites/2/queues/3');
    }

    public function testCreatesAQueueWithDefaults(): void
    {
        $this->queue(['data' => ['id' => 3]]);

        $queue = $this->ploi->servers(1)->sites(2)->queues();
        $queue->create();

        $this->assertRequest('post', 'servers/1/sites/2/queues', [
            'connection'      => 'database',
            'queue'           => 'default',
            'maximum_seconds' => 60,
            'sleep'           => 30,
            'processes'       => 1,
            'maximum_tries'   => 1,
        ]);

        $this->assertSame(3, $queue->getId());
    }

    public function testCreatesAQueueWithEveryOption(): void
    {
        $this->queue(['data' => ['id' => 3]]);

        $this->ploi->servers(1)->sites(2)->queues()->create('redis', 'emails', 120, 5, 4, 3);

        $this->assertRequest('post', 'servers/1/sites/2/queues', [
            'connection'      => 'redis',
            'queue'           => 'emails',
            'maximum_seconds' => 120,
            'sleep'           => 5,
            'processes'       => 4,
            'maximum_tries'   => 3,
        ]);
    }

    public function testRestartsAQueue(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->queues(3)->restart();

        $this->assertRequest('post', 'servers/1/sites/2/queues/3/restart');
    }

    public function testPausesAQueue(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->queues(3)->pause();

        $this->assertRequest('post', 'servers/1/sites/2/queues/3/toggle-pause');
    }

    public function testDeletesAQueue(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->queues(3)->delete();

        $this->assertRequest('delete', 'servers/1/sites/2/queues/3');
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
    public function testMethodsThatNeedAQueueIdThrowWithoutOne(string $method): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->servers(1)->sites(2)->queues()->{$method}();
    }
}
