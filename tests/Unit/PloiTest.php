<?php

declare(strict_types=1);

namespace Tests\Unit;

use Exception;
use Ploi\Exceptions\Http\InternalServerError;
use Ploi\Exceptions\Http\NotAllowed;
use Ploi\Exceptions\Http\NotFound;
use Ploi\Exceptions\Http\NotValid;
use Ploi\Exceptions\Http\PerformingMaintenance;
use Ploi\Exceptions\Http\TooManyAttempts;
use Ploi\Exceptions\Http\Unauthenticated;
use Ploi\Ploi;

class PloiTest extends TestCase
{
    public function testSendsTheTokenAndJsonHeaders(): void
    {
        $this->queue();

        $this->ploi->makeAPICall('servers');

        $request = $this->request();

        $this->assertSame('Bearer ' . self::TOKEN, $request->getHeaderLine('Authorization'));
        $this->assertSame('application/json', $request->getHeaderLine('Accept'));
        $this->assertSame('application/json', $request->getHeaderLine('Content-Type'));
    }

    public function testResolvesAgainstTheApiBaseUri(): void
    {
        $this->queue();

        $this->ploi->makeAPICall('servers/1/sites');

        $this->assertRequest('get', 'servers/1/sites');
    }

    public function testCanSetAndGetTheApiToken(): void
    {
        $this->assertSame(self::TOKEN, $this->ploi->getApiToken());

        $this->ploi->setApiToken('another-token');

        $this->assertSame('another-token', $this->ploi->getApiToken());
    }

    public function testKeepsTheHandlerWhenTheTokenIsReplaced(): void
    {
        $this->queue();

        $this->ploi->setApiToken('another-token');
        $this->ploi->makeAPICall('servers');

        $this->assertSame('Bearer another-token', $this->request()->getHeaderLine('Authorization'));
    }

    public function testRejectsAnUnsupportedHttpMethod(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid method type');

        $this->ploi->makeAPICall('servers', 'put');
    }

    public function testAcceptsTheSupportedHttpMethods(): void
    {
        $this->queueMany(4);

        foreach (['get', 'post', 'patch', 'delete'] as $index => $method) {
            $this->ploi->makeAPICall('servers', $method);

            $this->assertSame(strtoupper($method), $this->request($index)->getMethod());
        }
    }

    /**
     * @return array<string, array{0: int, 1: class-string<Exception>}>
     */
    public function statusCodeProvider(): array
    {
        return [
            '401 unauthenticated'      => [401, Unauthenticated::class],
            '404 not found'            => [404, NotFound::class],
            '405 not allowed'          => [405, NotAllowed::class],
            '422 not valid'            => [422, NotValid::class],
            '429 too many attempts'    => [429, TooManyAttempts::class],
            '500 internal server'      => [500, InternalServerError::class],
            '503 under maintenance'    => [503, PerformingMaintenance::class],
        ];
    }

    /**
     * @dataProvider statusCodeProvider
     *
     * @param class-string<Exception> $exception
     */
    public function testMapsStatusCodesToExceptions(int $status, string $exception): void
    {
        $this->queue(['message' => 'nope'], $status);

        $this->expectException($exception);

        $this->ploi->makeAPICall('servers');
    }

    public function testReturnsAResponseForASuccessfulCall(): void
    {
        $this->queue(['data' => ['id' => 42, 'name' => 'web-01']]);

        $response = $this->ploi->makeAPICall('servers/42');

        $this->assertSame(42, $response->getData()->id);
        $this->assertSame('web-01', $response->getData()->name);
        $this->assertSame(200, $response->getResponse()->getStatusCode());
        $this->assertArrayHasKey('json', $response->toArray());
    }

    public function testHandlerCanBeRemoved(): void
    {
        $this->assertInstanceOf(Ploi::class, $this->ploi->setHandler(null));
    }
}
