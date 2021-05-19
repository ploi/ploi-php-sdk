<?php

namespace Tests\Ploi\Resources;

use Tests\BaseTest;
use Ploi\Http\Response;
use Ploi\Resources\Server;
use Ploi\Exceptions\Resource\RequiresId;

/**
 * Class ServerTest
 *
 * @package Tests\Ploi\Resources
 */
class ServerTest extends BaseTest
{
    public function testInstanceOfServer()
    {
        $this->assertInstanceOf(Server::class, $this->getPloi()->server());
    }

    public function testBuildsUrlCorrectly()
    {
        $server = $this->getPloi()->server();

        $this->assertEquals('servers', $server->buildEndpoint());
        $this->assertEquals('servers/custom', $server->buildEndpoint('custom'));

        $server->setId(1);
        $this->assertEquals('servers/1', $server->buildEndpoint());
        $this->assertEquals('servers/1/endpoint', $server->buildEndpoint('endpoint'));
        $this->assertEquals('servers/1/endpoint', $server->buildEndpoint('/endpoint'));

        $server->setId();
        $this->assertEquals('servers/different-endpoint', $server->buildEndpoint('/different-endpoint'));
    }

    /**
     * @throws \Ploi\Exceptions\Http\InternalServerError
     * @throws \Ploi\Exceptions\Http\NotFound
     * @throws \Ploi\Exceptions\Http\NotValid
     * @throws \Ploi\Exceptions\Http\PerformingMaintenance
     * @throws \Ploi\Exceptions\Http\TooManyAttempts
     */
    public function testGetAllServers()
    {
        $servers = $this->getPloi()
                        ->server()
                        ->get();

        // Test that it's a valid response object
        $this->assertInstanceOf(Response::class, $servers);

        // Test the json object response
        $this->assertInstanceOf(\stdClass::class, $servers->getJson());

        // Test the array response
        $this->assertIsArray($servers->toArray());

        // Test to make sure that the data is an array
        $this->assertIsArray($servers->getJson()->data);
    }

    public function testGetPaginatedServers()
    {
        $serversPage1 = $this->getPloi()
            ->server()
            ->perPage(2)
            ->page(1);
        $serversPage2 = $this->getPloi()
            ->server()
            ->page(2, 2);

        // Test that it's a valid response object
        $this->assertInstanceOf(Response::class, $serversPage1);
        $this->assertInstanceOf(Response::class, $serversPage2);

        // Test the json object response
        $this->assertInstanceOf(\stdClass::class, $serversPage1->getJson());
        $this->assertInstanceOf(\stdClass::class, $serversPage2->getJson());

        // Test the array response
        $this->assertIsArray($serversPage1->toArray());
        $this->assertIsArray($serversPage2->toArray());

        // Test each response returns only 2 servers
        $this->assertCount(2, $serversPage1->getData());
        $this->assertCount(2, $serversPage2->getData());

        // Test first server returned in each response is different
        $this->assertNotEquals($serversPage1->getData()[0]->id, $serversPage2->getData()[0]->id);
    }

    /**
     * @throws \Ploi\Exceptions\Http\InternalServerError
     * @throws \Ploi\Exceptions\Http\NotFound
     * @throws \Ploi\Exceptions\Http\NotValid
     * @throws \Ploi\Exceptions\Http\PerformingMaintenance
     * @throws \Ploi\Exceptions\Http\TooManyAttempts
     */
    public function testGetSingleServer()
    {
        $serverResource = $this->getPloi()
                               ->server();

        // Get all servers and select the first one
        $allServers = $serverResource->get();
        $firstServer = $allServers->getJson()->data[0];

        if (!empty($firstServer)) {
            $serverId = $firstServer->id;

            // Get a single server through a pre-existing server resource
            $methodOne = $serverResource->get($serverId);

            // Get a single server through a new server resource
            $methodTwo = $this->getPloi()->server($serverId)->get();

            $this->assertEquals($serverId, $methodOne->getJson()->data->id);
            $this->assertEquals($serverId, $methodTwo->getJson()->data->id);
        }

        // Check that it throws a RequiresId error
        try {
            $this->getPloi()->server()->get();
        } catch (\Exception $exception) {
            $this->assertInstanceOf(RequiresId::class, $exception);
        }
    }

    /**
     * @throws \Ploi\Exceptions\Http\InternalServerError
     * @throws \Ploi\Exceptions\Http\NotFound
     * @throws \Ploi\Exceptions\Http\NotValid
     * @throws \Ploi\Exceptions\Http\PerformingMaintenance
     * @throws \Ploi\Exceptions\Http\TooManyAttempts
     * @throws \Ploi\Exceptions\Resource\RequiresId
     */
    public function testGetServerLogs()
    {
        $serverResource = $this->getPloi()
                               ->server();

        // Get all servers and select the first one
        $allServers = $serverResource->get();
        $firstServer = $allServers->getJson()->data[0];

        if (!empty($firstServer)) {
            $serverId = $firstServer->id;

            // Get a single server through a pre-existing server resource
            $methodOne = $serverResource->logs($serverId);

            // Get a single server through a new server resource
            $methodTwo = $this->getPloi()->server($serverId)->logs();

            $this->assertIsArray($methodOne->getJson()->data);
            $this->assertEquals($serverId, $methodOne->getJson()->data[0]->server_id);
            $this->assertEquals($serverId, $methodTwo->getJson()->data[0]->server_id);
        }

        // Check that it throws a RequiresId error
        try {
            $this->getPloi()->server()->logs();
        } catch (\Exception $exception) {
            $this->assertInstanceOf(RequiresId::class, $exception);
        }
    }

    public function testServerRestart()
    {
        $serverResource = $this->getPloi()
            ->server();

        // Get all servers and select the first one
        $allServers = $serverResource->get();
        $firstServer = $allServers->getJson()->data[0];

        if (!empty($firstServer)) {
            $serverId = $firstServer->id;

            // Get a single server through a pre-existing server resource
            $methodOne = $serverResource->restart($serverId);

            // Get a single server through a new server resource
            $methodTwo = $this->getPloi()->server($serverId)->restart();

            $this->assertTrue($methodOne->getResponse()->getStatusCode() === 200);
            $this->assertTrue($methodTwo->getResponse()->getStatusCode() === 200);
        }

        // Check that it throws a RequiresId error
        try {
            $this->getPloi()->server()->restart();
        } catch (\Exception $exception) {
            $this->assertInstanceOf(RequiresId::class, $exception);
        }
    }

    public function testGetServerMonitoring()
    {
        $serverResource = $this->getPloi()
            ->server();

        // Get all servers and select the first one
        $allServers = $serverResource->get();
        $firstServer = $allServers->getJson()->data[0];

        if (!empty($firstServer)) {
            $serverId = $firstServer->id;

            // Get a single server through a pre-existing server resource
            $methodOne = $serverResource->monitoring($serverId);

            // Get a single server through a new server resource
            $methodTwo = $this->getPloi()->server($serverId)->monitoring();

            $this->assertIsArray($methodOne->getJson()->data);
            $this->assertIsArray($methodTwo->getJson()->data);
        }

        // Check that it throws a RequiresId error
        try {
            $this->getPloi()->server()->monitoring();
        } catch (\Exception $exception) {
            $this->assertInstanceOf(RequiresId::class, $exception);
        }
    }
}
