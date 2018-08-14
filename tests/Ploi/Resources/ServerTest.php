<?php

namespace Tests\Ploi\Resources;

use Ploi\Resources\Server\Server;
use Tests\BaseTest;

/**
 * Class ServerTest
 * @package Tests\Ploi\Resources
 */
class ServerTest extends BaseTest
{
    public function testInstanceOfServer()
    {
        $this->assertInstanceOf(Server::class, $this->getPloi()->server());
    }
}