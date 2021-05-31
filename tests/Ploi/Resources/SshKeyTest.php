<?php

namespace Tests\Ploi\Resources;

use stdClass;
use Tests\BaseTest;
use Ploi\Http\Response;
use Ploi\Resources\Server;
use Ploi\Exceptions\Http\NotFound;

/**
 * Class SshKeyTest
 *
 * @package Tests\Ploi\Resources
 */
class SshKeyTest extends BaseTest
{
    /**
     * @var Server
     */
    private $server;

    public function setup(): void
    {
        parent::setup();

        $resource = $this->getPloi()->server();
        $allServers = $resource->get();
        if (!empty($allServers->getJson()->data)) {
            $this->server = $resource->setId($allServers->getJson()->data[0]->id);
        }
    }

    public function testGetAllSshKeys()
    {
        $resource = $this->server->sshKeys();

        $sshKeys = $resource->get();

        $this->assertInstanceOf(Response::class, $sshKeys);
        $this->assertIsArray($sshKeys->getJson()->data);
    }

    public function testGetPaginatedSshKeys()
    {
        $resource = $this->server->sshKeys();

        $sshKeysPage1 = $resource->perPage(5)->page();
        $sshKeysPage2 = $resource->page(2, 5);

        $this->assertInstanceOf(Response::class, $sshKeysPage1);
        $this->assertInstanceOf(Response::class, $sshKeysPage2);

        $this->assertIsArray($sshKeysPage1->getJson()->data);
        $this->assertIsArray($sshKeysPage2->getJson()->data);

        $this->assertEquals(1, $sshKeysPage1->getJson()->meta->current_page);
        $this->assertEquals(2, $sshKeysPage2->getJson()->meta->current_page);

        $this->assertEquals(5, $sshKeysPage1->getJson()->meta->per_page);
        $this->assertEquals(5, $sshKeysPage2->getJson()->meta->per_page);
    }

    public function testGetSingleSshKey()
    {
        $resource = $this->server->sshKeys();
        $sshKeys = $resource->get();

        if (!empty($sshKeys->getJson()->data[0])) {
            $sshKeyId = $sshKeys->getJson()->data[0]->id;

            $resource->setId($sshKeyId);
            $methodOne = $resource->get();
            $methodTwo = $this->server->sshKeys($sshKeyId)->get();
            $methodThree = $this->server->sshKeys()->get($sshKeyId);

            $this->assertInstanceOf(stdClass::class, $methodOne->getJson()->data);
            $this->assertEquals($sshKeyId, $methodOne->getJson()->data->id);
            $this->assertEquals($sshKeyId, $methodTwo->getJson()->data->id);
            $this->assertEquals($sshKeyId, $methodThree->getJson()->data->id);
        }
    }

    public function testCreateSshKey(): stdClass
    {
        $response = $this->server->sshKeys()->create(
            'SDK Test SSH Key',
            'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAAAgQDNnp3LYOjNdQzkFH27ggocdjQKxF+XF5NB0rl0jnmIYaA/Y28ONgivQED8NXpZutlpWvORcL+wPwwbdje8d+9uuOMaVO5c26HPczMS6EX0QpYyyEfw1BJbkKAMUBQC3ncqijzslrzNtl3y0R5nvOS6TO4ehsq/9/ntINztuGIdcw=='
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertNotEmpty($response->getData()->id);
        return $response->getData();
    }

    /**
     * @depends testCreateSshKey
     */
    public function testDeleteSshKey(stdClass $sshKey)
    {
        if (!empty($sshKey)) {
            $deleted = $this->server->sshKeys($sshKey->id)->delete();
            $this->assertTrue($deleted->getResponse()->getStatusCode() === 200);
        }
    }

    public function testDeleteInvalidSshKey()
    {
        try {
            $this->server->sshKeys(1)->delete();
        } catch (\Exception $e) {
            $this->assertInstanceOf(NotFound::class, $e);
        }

        try {
            $this->server->sshKeys()->delete(1);
        } catch (\Exception $e) {
            $this->assertInstanceOf(NotFound::class, $e);
        }
    }
}
