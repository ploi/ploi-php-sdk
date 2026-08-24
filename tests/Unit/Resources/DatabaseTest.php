<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Ploi\Resources\DatabaseBackup;
use Ploi\Resources\DatabaseUser;
use Tests\Unit\TestCase;

class DatabaseTest extends TestCase
{
    public function testListsDatabases(): void
    {
        $this->queue();

        $this->ploi->servers(1)->databases()->get();

        $this->assertRequest('get', 'servers/1/databases?page=1');
    }

    public function testGetsASingleDatabase(): void
    {
        $this->queue();

        $this->ploi->servers(1)->databases(2)->get();

        $this->assertRequest('get', 'servers/1/databases/2');
    }

    public function testCreatesADatabase(): void
    {
        $this->queue(['data' => ['id' => 3]]);

        $database = $this->ploi->servers(1)->databases();
        $database->create('shop', 'shop_user', 'secret');

        $this->assertRequest('post', 'servers/1/databases', [
            'name'        => 'shop',
            'user'        => 'shop_user',
            'password'    => 'secret',
            'description' => null,
            'site_id'     => null,
        ]);

        $this->assertSame(3, $database->getId());
    }

    public function testCreatesADatabaseWithADescriptionAndSite(): void
    {
        $this->queue(['data' => ['id' => 3]]);

        $this->ploi->servers(1)->databases()->create('shop', 'shop_user', 'secret', 'Webshop', 8);

        $this->assertRequest('post', 'servers/1/databases', [
            'name'        => 'shop',
            'user'        => 'shop_user',
            'password'    => 'secret',
            'description' => 'Webshop',
            'site_id'     => 8,
        ]);
    }

    public function testDeletesADatabase(): void
    {
        $this->queue();

        $this->ploi->servers(1)->databases(2)->delete();

        $this->assertRequest('delete', 'servers/1/databases/2');
    }

    public function testAcknowledgesADatabase(): void
    {
        $this->queue();

        $this->ploi->servers(1)->databases()->acknowledge('shop');

        $this->assertRequest('post', 'servers/1/databases/acknowledge', ['name' => 'shop']);
    }

    public function testForgetsADatabase(): void
    {
        $this->queue();

        $this->ploi->servers(1)->databases(2)->forget();

        $this->assertRequest('delete', 'servers/1/databases/2/forget');
    }

    public function testDuplicatesADatabase(): void
    {
        $this->queue();

        $this->ploi->servers(1)->databases(2)->duplicate('shop_copy', 'copy_user', 'secret');

        $this->assertRequest('post', 'servers/1/databases/2/duplicate', [
            'name'     => 'shop_copy',
            'user'     => 'copy_user',
            'password' => 'secret',
        ]);
    }

    public function testDuplicateRequiresAnId(): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->servers(1)->databases()->duplicate('shop_copy');
    }

    public function testExposesBackupsAndUsers(): void
    {
        $database = $this->ploi->servers(1)->databases(2);

        $this->assertInstanceOf(DatabaseBackup::class, $database->backups());
        $this->assertInstanceOf(DatabaseUser::class, $database->users());
    }
}
