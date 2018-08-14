<?php

namespace Tests\Ploi;

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
}
