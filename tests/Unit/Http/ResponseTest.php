<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use Ploi\Http\Response;
use stdClass;
use Tests\Unit\TestCase;

class ResponseTest extends TestCase
{
    public function testWrapsTheGuzzleResponse(): void
    {
        $this->queue(['data' => ['id' => 1]]);

        $response = $this->ploi->makeAPICall('servers/1');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->getResponse()->getStatusCode());
    }

    public function testDecodesTheJsonBody(): void
    {
        $this->queue(['data' => ['id' => 1, 'name' => 'web-01']]);

        $json = $this->ploi->makeAPICall('servers/1')->getJson();

        $this->assertInstanceOf(stdClass::class, $json);
        $this->assertSame('web-01', $json->data->name);
    }

    public function testGetDataUnwrapsTheDataKey(): void
    {
        $this->queue(['data' => ['id' => 1]]);

        $this->assertSame(1, $this->ploi->makeAPICall('servers/1')->getData()->id);
    }

    public function testGetDataFallsBackToTheWholeBodyWithoutADataKey(): void
    {
        $this->queue(['id' => 1]);

        $this->assertSame(1, $this->ploi->makeAPICall('servers/1')->getData()->id);
    }

    public function testToArrayHoldsBothTheJsonAndTheResponse(): void
    {
        $this->queue(['data' => ['id' => 1]]);

        $array = $this->ploi->makeAPICall('servers/1')->toArray();

        $this->assertArrayHasKey('json', $array);
        $this->assertArrayHasKey('response', $array);
        $this->assertSame(1, $array['json']->data->id);
    }
}
