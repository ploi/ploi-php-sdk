<?php

namespace Tests\Ploi;

use Exception;
use Tests\BaseTest;
use Ploi\Exceptions\Http\NotFound;
use Ploi\Exceptions\Http\NotAllowed;
use Ploi\Exceptions\Http\Unauthenticated;

/**
 * Class PloiTest
 * @package Tests\Ploi
 */
class PloiTest extends BaseTest
{
    public function testCanGetAPiToken()
    {
        $this->assertEquals($_ENV['API_TOKEN'], $this->getPloi()->getApiToken());
    }

    public function testCanSetApiToken()
    {
        $newToken = "NEW_TOKEN";

        $this->getPloi()->setApiToken($newToken);

        $this->assertEquals($newToken, $this->getPloi()->getApiToken());
    }

    public function testValidApiMethod()
    {
        $methods = ['get', 'post', 'delete'];

        foreach ($methods as $method) {
            try {
                $path = $method === 'delete' ? 'servers/1' : 'servers';
                $this->getPloi()->makeAPICall($path, $method);
            } catch (Exception $exception) {
                $this->assertNotInstanceOf(NotAllowed::class, $exception);
            }
        }
    }

    public function testInvalidApiMethod()
    {
        $method = 'PPOST';
        try {
            $this->getPloi()->makeAPICall('url', $method);
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals('Invalid method type', $e->getMessage());
        }
    }

    public function testThrows404()
    {
        try {
            $this->getPloi()->makeAPICall('servers/servers');
        } catch (NotFound $e) {
            $this->assertInstanceOf(NotFound::class, $e);
        }
    }

    public function testThrowsUnauthenticated()
    {
        $this->getPloi()->setApiToken('Invalid');

        try {
            $this->getPloi()->server()->get();
        } catch (Unauthenticated $e) {
            $this->assertInstanceOf(Unauthenticated::class, $e);
        }
    }
}
