<?php

namespace Tests\Ploi;

use Exception;
use Ploi\Exceptions\Http\NotFound;
use Ploi\Exceptions\Http\Unauthenticated;
use Tests\BaseTest;

/**
 * Class PloiTest
 * @package Tests\Ploi
 */
class PloiTest extends BaseTest
{

    public function testCanGetAPiToken()
    {
        $this->assertEquals(getenv('API_TOKEN'), $this->getPloi()->getApiToken());
    }

    public function testCanSetApiToken()
    {
        $newToken = "NEW_TOKEN";

        $this->getPloi()->setApiToken($newToken);

        $this->assertEquals($newToken, $this->getPloi()->getApiToken());
    }

    public function testValidApiMethod()
    {
        $methods = ['get', 'post', 'patch', 'delete'];

        foreach ($methods as $method) {
            $response = $this->getPloi()->makeAPICall('servers', $method);
            $this->assertNotInstanceOf(Exception::class, $response);

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
            $this->getPloi()->makeAPICall('url', 'get');
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
