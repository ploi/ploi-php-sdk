<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Tests\Unit\TestCase;

class SystemUserTest extends TestCase
{
    public function testListsSystemUsers(): void
    {
        $this->queue();

        $this->ploi->servers(1)->systemUsers()->get();

        $this->assertRequest('get', 'servers/1/system-users?page=1');
    }

    public function testGetsASingleSystemUser(): void
    {
        $this->queue();

        $this->ploi->servers(1)->systemUsers(2)->get();

        $this->assertRequest('get', 'servers/1/system-users/2');
    }

    public function testCreatesASystemUser(): void
    {
        $this->queue(['data' => ['id' => 2]]);

        $user = $this->ploi->servers(1)->systemUsers();
        $user->create('deployer');

        $this->assertRequest('post', 'servers/1/system-users', [
            'name' => 'deployer',
            'sudo' => false,
        ]);

        $this->assertSame(2, $user->getId());
    }

    public function testCreatesASudoSystemUser(): void
    {
        $this->queue(['data' => ['id' => 2]]);

        $this->ploi->servers(1)->systemUsers()->create('deployer', true);

        $this->assertRequest('post', 'servers/1/system-users', [
            'name' => 'deployer',
            'sudo' => true,
        ]);
    }

    public function testDeletesASystemUser(): void
    {
        $this->queue();

        $this->ploi->servers(1)->systemUsers(2)->delete();

        $this->assertRequest('delete', 'servers/1/system-users/2');
    }

    public function testDeleteRequiresAnId(): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->servers(1)->systemUsers()->delete();
    }
}
