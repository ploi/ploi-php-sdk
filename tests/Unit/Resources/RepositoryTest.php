<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Tests\Unit\TestCase;

class RepositoryTest extends TestCase
{
    public function testGetsTheRepository(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->repository()->get();

        $this->assertRequest('get', 'servers/1/sites/2/repository');
    }

    public function testInstallsARepository(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->repository()->install('github', 'main', 'ploi/ploi-php-sdk');

        $this->assertRequest('post', 'servers/1/sites/2/repository', [
            'provider' => 'github',
            'branch'   => 'main',
            'name'     => 'ploi/ploi-php-sdk',
        ]);
    }

    public function testDeletesTheRepository(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->repository()->delete();

        $this->assertRequest('delete', 'servers/1/sites/2/repository');
    }

    public function testTogglesQuickDeploy(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->repository()->toggleQuickDeploy();

        $this->assertRequest('post', 'servers/1/sites/2/repository/quick-deploy');
    }
}
