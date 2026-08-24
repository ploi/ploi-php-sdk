<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Tests\Unit\TestCase;

class AppTest extends TestCase
{
    public function testGetsTheApp(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->app()->get();

        $this->assertRequest('get', 'servers/1/sites/2');
    }

    public function testInstallsAnApp(): void
    {
        $this->queue(['data' => ['id' => 3, 'type' => 'wordpress']]);

        $app = $this->ploi->servers(1)->sites(2)->app();
        $data = $app->install();

        $this->assertRequest('post', 'servers/1/sites/2/wordpress', ['create_database' => false]);
        $this->assertSame('wordpress', $data->type);
        $this->assertSame(3, $app->getId());
    }

    public function testInstallsAnAppWithADatabase(): void
    {
        $this->queue(['data' => ['id' => 3]]);

        $this->ploi->servers(1)->sites(2)->app()->install('matomo', ['create_database' => true]);

        $this->assertRequest('post', 'servers/1/sites/2/matomo', ['create_database' => true]);
    }

    public function testInstallReturnsTheDecodedErrorWhenTheApiRejectsIt(): void
    {
        $this->queue(['message' => 'Site already has an app'], 422);

        $result = $this->ploi->servers(1)->sites(2)->app()->install();

        $this->assertSame('Site already has an app', $result->message);
    }

    public function testUninstallsAnApp(): void
    {
        $this->queue();

        $result = $this->ploi->servers(1)->sites(2)->app()->uninstall('wordpress');

        $this->assertRequest('delete', 'servers/1/sites/2/wordpress');
        $this->assertTrue($result);
    }
}
