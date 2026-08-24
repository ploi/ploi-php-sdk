<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Tests\Unit\TestCase;

class NetworkRuleTest extends TestCase
{
    public function testListsNetworkRules(): void
    {
        $this->queue();

        $this->ploi->servers(1)->networkRules()->get();

        $this->assertRequest('get', 'servers/1/network-rules?page=1');
    }

    public function testGetsASingleNetworkRule(): void
    {
        $this->queue();

        $this->ploi->servers(1)->networkRules(2)->get();

        $this->assertRequest('get', 'servers/1/network-rules/2');
    }

    public function testCreatesANetworkRule(): void
    {
        $this->queue(['data' => ['id' => 2]]);

        $rule = $this->ploi->servers(1)->networkRules();
        $rule->create('http', 80);

        $this->assertRequest('post', 'servers/1/network-rules', [
            'name'            => 'http',
            'port'            => 80,
            'type'            => 'tcp',
            'rule_type'       => 'allow',
            'from_ip_address' => null,
        ]);

        $this->assertSame(2, $rule->getId());
    }

    public function testCreatesADenyRuleForASingleIp(): void
    {
        $this->queue(['data' => ['id' => 2]]);

        $this->ploi->servers(1)->networkRules()->create('block', 22, 'udp', '1.2.3.4', 'deny');

        $this->assertRequest('post', 'servers/1/network-rules', [
            'name'            => 'block',
            'port'            => 22,
            'type'            => 'udp',
            'rule_type'       => 'deny',
            'from_ip_address' => '1.2.3.4',
        ]);
    }

    public function testDeletesANetworkRule(): void
    {
        $this->queue();

        $this->ploi->servers(1)->networkRules(2)->delete();

        $this->assertRequest('delete', 'servers/1/network-rules/2');
    }

    public function testDeleteRequiresAnId(): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->servers(1)->networkRules()->delete();
    }
}
