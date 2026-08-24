<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Tests\Unit\TestCase;

class InsightTest extends TestCase
{
    public function testListsInsights(): void
    {
        $this->queue();

        $this->ploi->servers(1)->insights()->get();

        $this->assertRequest('get', 'servers/1/insights?page=1');
    }

    public function testGetsASingleInsight(): void
    {
        $this->queue();

        $this->ploi->servers(1)->insights(2)->get();

        $this->assertRequest('get', 'servers/1/insights/2');
    }

    public function testGetsInsightDetail(): void
    {
        $this->queue();

        $this->ploi->servers(1)->insights(2)->detail();

        $this->assertRequest('get', 'servers/1/insights/2/detail');
    }

    public function testAutomaticallyFixesAnInsight(): void
    {
        $this->queue();

        $this->ploi->servers(1)->insights(2)->automaticallyFix();

        $this->assertRequest('post', 'servers/1/insights/2/automatically-fix');
    }

    public function testIgnoresAnInsight(): void
    {
        $this->queue();

        $this->ploi->servers(1)->insights(2)->ignore();

        $this->assertRequest('post', 'servers/1/insights/2/ignore');
    }

    public function testDeletesAnInsight(): void
    {
        $this->queue();

        $this->ploi->servers(1)->insights(2)->delete();

        $this->assertRequest('delete', 'servers/1/insights/2');
    }

    /**
     * @return array<string, array{0: string}>
     */
    public function requiresIdProvider(): array
    {
        return [
            'detail'          => ['detail'],
            'automaticallyFix' => ['automaticallyFix'],
            'ignore'          => ['ignore'],
            'delete'          => ['delete'],
        ];
    }

    /**
     * @dataProvider requiresIdProvider
     */
    public function testMethodsThatNeedAnInsightIdThrowWithoutOne(string $method): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->servers(1)->insights()->{$method}();
    }
}
