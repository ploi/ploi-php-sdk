<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Tests\Unit\TestCase;

class DatabaseUserTest extends TestCase
{
    public function testListsDatabaseUsers(): void
    {
        $this->queue();

        $this->ploi->servers(1)->databases(2)->users()->get();

        $this->assertRequest('get', 'servers/1/databases/2/users?page=1');
    }

    public function testGetsASingleDatabaseUser(): void
    {
        $this->queue();

        $this->ploi->servers(1)->databases(2)->users(3)->get();

        $this->assertRequest('get', 'servers/1/databases/2/users/3');
    }

    public function testCreatesADatabaseUser(): void
    {
        $this->queue();

        $this->ploi->servers(1)->databases(2)->users()->create('reader', 'secret');

        $this->assertRequest('post', 'servers/1/databases/2/users', [
            'user'      => 'reader',
            'password'  => 'secret',
            'remote'    => false,
            'remote_ip' => '%',
            'readonly'  => false,
        ]);
    }

    public function testCreatesARemoteReadonlyDatabaseUser(): void
    {
        $this->queue();

        $this->ploi->servers(1)->databases(2)->users()->create('reader', 'secret', true, '1.2.3.4', true);

        $this->assertRequest('post', 'servers/1/databases/2/users', [
            'user'      => 'reader',
            'password'  => 'secret',
            'remote'    => true,
            'remote_ip' => '1.2.3.4',
            'readonly'  => true,
        ]);
    }

    public function testDeletesADatabaseUser(): void
    {
        $this->queue();

        $this->ploi->servers(1)->databases(2)->users(3)->delete();

        $this->assertRequest('delete', 'servers/1/databases/2/users/3');
    }

    public function testDeleteRequiresAnId(): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->servers(1)->databases(2)->users()->delete();
    }
}
