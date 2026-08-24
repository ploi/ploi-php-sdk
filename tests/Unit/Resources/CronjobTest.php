<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Tests\Unit\TestCase;

class CronjobTest extends TestCase
{
    public function testListsCronjobs(): void
    {
        $this->queue();

        $this->ploi->servers(1)->cronjobs()->get();

        $this->assertRequest('get', 'servers/1/crontabs?page=1');
    }

    public function testGetsASingleCronjob(): void
    {
        $this->queue();

        $this->ploi->servers(1)->cronjobs(2)->get();

        $this->assertRequest('get', 'servers/1/crontabs/2');
    }

    public function testCreatesACronjob(): void
    {
        $this->queue(['data' => ['id' => 2]]);

        $cronjob = $this->ploi->servers(1)->cronjobs();
        $cronjob->create('php artisan schedule:run', '* * * * *');

        $this->assertRequest('post', 'servers/1/crontabs', [
            'command'   => 'php artisan schedule:run',
            'frequency' => '* * * * *',
            'user'      => 'ploi',
        ]);

        $this->assertSame(2, $cronjob->getId());
    }

    public function testCreatesACronjobForAnotherUser(): void
    {
        $this->queue(['data' => ['id' => 2]]);

        $this->ploi->servers(1)->cronjobs()->create('backup.sh', '0 3 * * *', 'deployer');

        $this->assertRequest('post', 'servers/1/crontabs', [
            'command'   => 'backup.sh',
            'frequency' => '0 3 * * *',
            'user'      => 'deployer',
        ]);
    }

    public function testDeletesACronjob(): void
    {
        $this->queue();

        $this->ploi->servers(1)->cronjobs(2)->delete();

        $this->assertRequest('delete', 'servers/1/crontabs/2');
    }
}
