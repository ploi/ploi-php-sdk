<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Tests\Unit\TestCase;

class DeploymentTest extends TestCase
{
    public function testDeploys(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->deployment()->deploy();

        $this->assertRequest('post', 'servers/1/sites/2/deploy');
    }

    public function testDeploysToProduction(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->deployment()->deployToProduction();

        $this->assertRequest('post', 'servers/1/sites/2/deploy-to-production');
    }

    public function testGetsTheDeployScript(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->deployment()->deployScript();

        $this->assertRequest('get', 'servers/1/sites/2/deploy/script');
    }

    public function testUpdatesTheDeployScript(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->deployment()->updateDeployScript('php artisan migrate');

        $this->assertRequest('patch', 'servers/1/sites/2/deploy/script', [
            'deploy_script' => 'php artisan migrate',
        ]);
    }
}
