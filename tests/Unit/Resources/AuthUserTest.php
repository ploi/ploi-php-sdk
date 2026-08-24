<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Tests\Unit\TestCase;

class AuthUserTest extends TestCase
{
    public function testListsAuthUsers(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->authUser()->get();

        $this->assertRequest('get', 'servers/1/sites/2/auth-users?page=1');
    }

    public function testGetsASingleAuthUser(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->authUser(3)->get();

        $this->assertRequest('get', 'servers/1/sites/2/auth-users/3');
    }

    public function testCreatesAnAuthUser(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->authUser()->create('admin', 'secret');

        $this->assertRequest('post', 'servers/1/sites/2/auth-users', [
            'name'     => 'admin',
            'password' => 'secret',
            'path'     => null,
        ]);
    }

    public function testCreatesAnAuthUserForAPath(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->authUser()->create('admin', 'secret', '/staging');

        $this->assertRequest('post', 'servers/1/sites/2/auth-users', [
            'name'     => 'admin',
            'password' => 'secret',
            'path'     => '/staging',
        ]);
    }

    public function testDeletesAnAuthUser(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->authUser(3)->delete();

        $this->assertRequest('delete', 'servers/1/sites/2/auth-users/3');
    }

    public function testDeleteRequiresAnId(): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->servers(1)->sites(2)->authUser()->delete();
    }
}
