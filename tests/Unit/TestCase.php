<?php

declare(strict_types=1);

namespace Tests\Unit;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Ploi\Ploi;
use Psr\Http\Message\RequestInterface;

/**
 * Base class for tests that never touch the network.
 *
 * A Guzzle MockHandler is plugged into the real client through Ploi::setHandler(),
 * so the client is still built by production code: base URI, headers and the
 * http_errors setting are the ones users get.
 */
abstract class TestCase extends PHPUnitTestCase
{
    protected const BASE_URI = 'https://ploi.io/api/';
    protected const TOKEN = 'test-token';

    /**
     * @var Ploi
     */
    protected $ploi;

    /**
     * @var MockHandler
     */
    private $mockHandler;

    /**
     * Every request/response pair that went through the client
     *
     * @var array<int, array<string, mixed>>
     */
    private $transactions = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->transactions = [];
        $this->mockHandler = new MockHandler();

        $stack = HandlerStack::create($this->mockHandler);
        $stack->push(Middleware::history($this->transactions));

        $this->ploi = (new Ploi(self::TOKEN))->setHandler($stack);
    }

    /**
     * Queues a JSON response. The default body is enough for the create()
     * methods, which read the new id straight off the response.
     *
     * @param array<string, mixed> $data
     */
    protected function queue(array $data = ['data' => ['id' => 1]], int $status = 200): self
    {
        return $this->queueRaw((string) json_encode($data), $status);
    }

    protected function queueRaw(string $body, int $status = 200): self
    {
        $this->mockHandler->append(
            new Response($status, ['Content-Type' => 'application/json'], $body)
        );

        return $this;
    }

    /**
     * Queues the same default response $count times, for chains that make
     * more than one call.
     */
    protected function queueMany(int $count): self
    {
        for ($i = 0; $i < $count; $i++) {
            $this->queue();
        }

        return $this;
    }

    protected function request(int $index = 0): RequestInterface
    {
        $this->assertArrayHasKey($index, $this->transactions, "No request was made at index {$index}");

        /** @var RequestInterface $request */
        $request = $this->transactions[$index]['request'];

        return $request;
    }

    /**
     * Asserts the verb, the full URI and optionally the decoded JSON body of a request.
     *
     * @param array<string, mixed>|null $body
     */
    protected function assertRequest(string $method, string $path, ?array $body = null, int $index = 0): void
    {
        $request = $this->request($index);

        $this->assertSame(strtoupper($method), $request->getMethod());
        $this->assertSame(self::BASE_URI . ltrim($path, '/'), (string) $request->getUri());

        if ($body !== null) {
            $this->assertSame($body, json_decode((string) $request->getBody(), true));
        }
    }

    protected function assertRequestCount(int $count): void
    {
        $this->assertCount($count, $this->transactions);
    }

    protected function assertNoBody(int $index = 0): void
    {
        $this->assertSame('', (string) $this->request($index)->getBody());
    }
}
