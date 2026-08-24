<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Ploi\Resources\Server;
use Tests\Unit\TestCase;

class ServerTest extends TestCase
{
    public function testListsServers(): void
    {
        $this->queue();

        $this->ploi->servers()->get();

        $this->assertRequest('get', 'servers?page=1');
    }

    public function testPaginatesServers(): void
    {
        $this->queue();

        $this->ploi->servers()->perPage(5)->page(2);

        $this->assertRequest('get', 'servers?page=2&per_page=5');
    }

    public function testGetsASingleServer(): void
    {
        $this->queue();

        $this->ploi->servers(1)->get();

        $this->assertRequest('get', 'servers/1');
    }

    public function testGetsASingleServerByArgument(): void
    {
        $this->queue();

        $this->ploi->servers()->get(1);

        $this->assertRequest('get', 'servers/1');
    }

    public function testBuildsUrlCorrectly(): void
    {
        $server = $this->ploi->server();

        $this->assertSame('servers', $server->buildEndpoint());
        $this->assertSame('servers/custom', $server->buildEndpoint('custom'));

        $server->setId(1);

        $this->assertSame('servers/1', $server->buildEndpoint());
        $this->assertSame('servers/1/endpoint', $server->buildEndpoint('endpoint'));
        $this->assertSame('servers/1/endpoint', $server->buildEndpoint('/endpoint'));

        $server->setId();

        $this->assertSame('servers/different-endpoint', $server->buildEndpoint('/different-endpoint'));
    }

    public function testCreatesAServer(): void
    {
        $this->queue(['data' => ['id' => 7]]);

        $server = $this->ploi->servers();
        $server->create('web-01', 3, 'ams3', 's-1vcpu-1gb');

        $this->assertRequest('post', 'servers', [
            'name'            => 'web-01',
            'plan'            => 's-1vcpu-1gb',
            'region'          => 'ams3',
            'credential'      => 3,
            'type'            => 'server',
            'database_type'   => 'mysql',
            'webserver_type'  => 'nginx',
            'php_version'     => '7.4',
        ]);

        $this->assertSame(7, $server->getId());
    }

    public function testCreateAcceptsOverrides(): void
    {
        $this->queue(['data' => ['id' => 7]]);

        $this->ploi->servers()->create('web-01', 3, 'ams3', 's-1vcpu-1gb', [
            'php_version'   => '8.3',
            'database_type' => 'postgresql',
        ]);

        $body = json_decode((string) $this->request()->getBody(), true);

        $this->assertSame('8.3', $body['php_version']);
        $this->assertSame('postgresql', $body['database_type']);
    }

    public function testCreatesACustomServer(): void
    {
        // createCustom() reads the id off the root of the response, not off data
        $this->queue(['id' => 9]);

        $server = $this->ploi->servers();
        $server->createCustom('1.2.3.4', ['php_version' => '8.3']);

        $this->assertRequest('post', 'servers/custom', [
            'ip'            => '1.2.3.4',
            'type'          => 'server',
            'database_type' => 'mysql',
            'php_version'   => '8.3',
        ]);

        $this->assertSame(9, $server->getId());
    }

    public function testStartsInstallationById(): void
    {
        $this->queue();

        $this->ploi->servers(1)->startInstallation();

        $this->assertRequest('post', 'servers/custom/1/start');
    }

    public function testStartsInstallationByUrl(): void
    {
        $this->queue();

        $this->ploi->servers()->startInstallation('servers/custom/abc/start');

        $this->assertRequest('post', 'servers/custom/abc/start');
    }

    public function testStartInstallationRequiresAnIdOrUrl(): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->servers()->startInstallation();
    }

    public function testDeletesAServer(): void
    {
        $this->queue();

        $this->ploi->servers(1)->delete();

        $this->assertRequest('delete', 'servers/1');
    }

    public function testGetsLogs(): void
    {
        $this->queue();

        $this->ploi->servers(1)->logs();

        $this->assertRequest('get', 'servers/1/logs');
    }

    public function testGetsMonitoring(): void
    {
        $this->queue();

        $this->ploi->servers(1)->monitoring();

        $this->assertRequest('get', 'servers/1/monitor');
    }

    public function testRestartsAServer(): void
    {
        $this->queue();

        $this->ploi->servers(1)->restart();

        $this->assertRequest('post', 'servers/1/restart');
    }

    public function testGetsPhpVersions(): void
    {
        $this->queue();

        $this->ploi->servers(1)->phpVersions();

        $this->assertRequest('get', 'servers/1/php/versions');
    }

    public function testInstallsAPhpVersion(): void
    {
        $this->queue();

        $this->ploi->servers(1)->installPhpVersion('8.3');

        $this->assertRequest('post', 'servers/1/php/install', ['version' => '8.3']);
    }

    public function testSwitchesThePhpCliVersion(): void
    {
        $this->queue();

        $this->ploi->servers(1)->switchPhpCliVersion('8.3');

        $this->assertRequest('post', 'servers/1/php/cli-version', ['version' => '8.3']);
    }

    public function testRefreshesOpcacheThroughTheDeprecatedMethod(): void
    {
        $this->queue();

        $this->ploi->servers(1)->refreshOpcache();

        $this->assertRequest('post', 'servers/1/refresh-opcache');
    }

    public function testEnablesOpcacheThroughTheDeprecatedMethod(): void
    {
        $this->queue();

        $this->ploi->servers(1)->enableOpcache();

        $this->assertRequest('post', 'servers/1/enable-opcache');
    }

    public function testDisablesOpcacheThroughTheDeprecatedMethod(): void
    {
        $this->queue();

        $this->ploi->servers(1)->disableOpcache();

        $this->assertRequest('delete', 'servers/1/disable-opcache');
    }

    public function testRunsAOneOffScript(): void
    {
        $this->queue();

        $this->ploi->servers(1)->runOneOffScript('npm install -g pm2');

        $this->assertRequest('post', 'servers/1/scripts/run', ['content' => 'npm install -g pm2']);
    }

    public function testRunsAOneOffScriptAsAUser(): void
    {
        $this->queue();

        $this->ploi->servers(1)->runOneOffScript('whoami', 'deployer');

        $this->assertRequest('post', 'servers/1/scripts/run', [
            'content' => 'whoami',
            'user'    => 'deployer',
        ]);
    }

    public function testGetsAScriptExecution(): void
    {
        $this->queue();

        $this->ploi->servers(1)->scriptExecution('3f4c9e9a-8b4e-4f0e-9d3b-2f6f2c1a7d42');

        $this->assertRequest('get', 'servers/1/scripts/run/3f4c9e9a-8b4e-4f0e-9d3b-2f6f2c1a7d42');
    }

    public function testSearchesServers(): void
    {
        $this->queue();

        $this->ploi->servers()->search('web');

        $this->assertRequest('get', 'servers?search=web');
    }

    /**
     * @return array<string, array{0: string, 1: array<int, mixed>}>
     */
    public function requiresIdProvider(): array
    {
        return [
            'delete'            => ['delete', []],
            'logs'              => ['logs', []],
            'monitoring'        => ['monitoring', []],
            'restart'           => ['restart', []],
            'phpVersions'       => ['phpVersions', []],
            'installPhpVersion' => ['installPhpVersion', ['8.3']],
            'runOneOffScript'   => ['runOneOffScript', ['whoami']],
            'scriptExecution'   => ['scriptExecution', ['uuid']],
        ];
    }

    /**
     * @dataProvider requiresIdProvider
     *
     * @param array<int, mixed> $arguments
     */
    public function testMethodsThatNeedAServerIdThrowWithoutOne(string $method, array $arguments): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->servers()->{$method}(...$arguments);
    }

    public function testServersIsAnAliasForServer(): void
    {
        $this->assertInstanceOf(Server::class, $this->ploi->server());
        $this->assertInstanceOf(Server::class, $this->ploi->servers());
    }
}
