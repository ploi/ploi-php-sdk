<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Tests\Unit\TestCase;

class SshKeyTest extends TestCase
{
    public function testListsSshKeys(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sshKeys()->get();

        $this->assertRequest('get', 'servers/1/ssh-keys?page=1');
    }

    public function testGetsASingleSshKey(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sshKeys(2)->get();

        $this->assertRequest('get', 'servers/1/ssh-keys/2');
    }

    public function testCreatesAnSshKey(): void
    {
        $this->queue(['data' => ['id' => 2]]);

        $key = $this->ploi->servers(1)->sshKeys();
        $key->create('laptop', 'ssh-ed25519 AAAA');

        $this->assertRequest('post', 'servers/1/ssh-keys', [
            'name'        => 'laptop',
            'key'         => 'ssh-ed25519 AAAA',
            'system_user' => null,
        ]);

        $this->assertSame(2, $key->getId());
    }

    public function testCreatesAnSshKeyForASystemUser(): void
    {
        $this->queue(['data' => ['id' => 2]]);

        $this->ploi->servers(1)->sshKeys()->create('laptop', 'ssh-ed25519 AAAA', 'deployer');

        $this->assertRequest('post', 'servers/1/ssh-keys', [
            'name'        => 'laptop',
            'key'         => 'ssh-ed25519 AAAA',
            'system_user' => 'deployer',
        ]);
    }

    public function testDeletesAnSshKey(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sshKeys(2)->delete();

        $this->assertRequest('delete', 'servers/1/ssh-keys/2');
    }

    public function testDeleteRequiresAnId(): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->servers(1)->sshKeys()->delete();
    }
}
