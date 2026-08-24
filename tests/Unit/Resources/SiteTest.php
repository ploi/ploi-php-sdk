<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Ploi\Resources\Alias;
use Ploi\Resources\App;
use Ploi\Resources\AuthUser;
use Ploi\Resources\Certificate;
use Ploi\Resources\Deployment;
use Ploi\Resources\Environment;
use Ploi\Resources\FastCgi;
use Ploi\Resources\Monitors;
use Ploi\Resources\NginxConfiguration;
use Ploi\Resources\Queue;
use Ploi\Resources\Redirect;
use Ploi\Resources\Repository;
use Ploi\Resources\Robot;
use Ploi\Resources\Tenant;
use Tests\Unit\TestCase;

class SiteTest extends TestCase
{
    public function testListsSites(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites()->get();

        $this->assertRequest('get', 'servers/1/sites?page=1');
    }

    public function testPaginatesSites(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites()->perPage(10)->page(3);

        $this->assertRequest('get', 'servers/1/sites?page=3&per_page=10');
    }

    public function testGetsASingleSite(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->get();

        $this->assertRequest('get', 'servers/1/sites/2');
    }

    public function testCreatesASite(): void
    {
        $this->queue(['data' => ['id' => 4]]);

        $site = $this->ploi->servers(1)->sites();
        $site->create('example.com');

        $this->assertRequest('post', 'servers/1/sites', [
            'root_domain'       => 'example.com',
            'web_directory'     => '/public',
            'project_root'      => null,
            'system_user'       => null,
            'webserver_template' => null,
            'project_type'      => null,
            'webhook_url'       => null,
        ]);

        $this->assertSame(4, $site->getId());
    }

    public function testCreatesASiteWithEveryOption(): void
    {
        $this->queue(['data' => ['id' => 4]]);

        $this->ploi->servers(1)->sites()->create(
            'example.com',
            '/dist',
            '/app',
            'deployer',
            8,
            'laravel',
            'https://hooks.example.com/ploi'
        );

        $this->assertRequest('post', 'servers/1/sites', [
            'root_domain'       => 'example.com',
            'web_directory'     => '/dist',
            'project_root'      => '/app',
            'system_user'       => 'deployer',
            'webserver_template' => 8,
            'project_type'      => 'laravel',
            'webhook_url'       => 'https://hooks.example.com/ploi',
        ]);
    }

    public function testUpdatesASite(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->update('new.example.com');

        $this->assertRequest('patch', 'servers/1/sites/2', ['root_domain' => 'new.example.com']);
    }

    public function testDeletesASite(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->delete();

        $this->assertRequest('delete', 'servers/1/sites/2');
    }

    public function testGetsSiteLogs(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->logs();

        $this->assertRequest('get', 'servers/1/sites/2/log');
    }

    public function testSetsThePhpVersion(): void
    {
        $this->queue(['data' => ['id' => 2]]);

        $this->ploi->servers(1)->sites(2)->phpVersion('8.3');

        $this->assertRequest('post', 'servers/1/sites/2/php-version', ['php_version' => '8.3']);
    }

    public function testTestsTheDomain(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->testDomain();

        $this->assertRequest('get', 'servers/1/sites/2/test-domain');
    }

    public function testEnablesTheTestDomain(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->enableTestDomain();

        $this->assertRequest('post', 'servers/1/sites/2/test-domain');
    }

    public function testDisablesTheTestDomain(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->disableTestDomain();

        $this->assertRequest('delete', 'servers/1/sites/2/test-domain');
    }

    public function testSuspendsASite(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->suspend();

        $this->assertRequest('post', 'servers/1/sites/2/suspend');
        $this->assertNoBody();
    }

    public function testSuspendsASiteWithAReason(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->suspend(null, 'unpaid');

        $this->assertRequest('post', 'servers/1/sites/2/suspend', ['reason' => 'unpaid']);
    }

    public function testResumesASite(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->resume();

        $this->assertRequest('post', 'servers/1/sites/2/resume');
    }

    public function testGetsHorizonStatistics(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->horizonStatistics();

        $this->assertRequest('get', 'servers/1/sites/2/laravel/horizon/stats');
    }

    public function testGetsHorizonStatisticsForAType(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->horizonStatistics('workload');

        $this->assertRequest('get', 'servers/1/sites/2/laravel/horizon/workload');
    }

    public function testClonesASite(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->clone(5, 'clone.example.com');

        $this->assertRequest('post', 'servers/1/sites/2/clone', [
            'clone_to_server' => 5,
            'domain'          => 'clone.example.com',
        ]);
    }

    public function testResetsPermissions(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->resetPermissions();

        $this->assertRequest('post', 'servers/1/sites/2/permission-reset');
    }

    public function testSearchesSites(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites()->search('example');

        $this->assertRequest('get', 'servers/1/sites?search=example');
    }

    /**
     * @return array<string, array{0: string, 1: array<int, mixed>}>
     */
    public function requiresIdProvider(): array
    {
        return [
            'update'            => ['update', ['example.com']],
            'logs'              => ['logs', []],
            'testDomain'        => ['testDomain', []],
            'enableTestDomain'  => ['enableTestDomain', []],
            'disableTestDomain' => ['disableTestDomain', []],
            'suspend'           => ['suspend', []],
            'resume'            => ['resume', []],
            'clone'             => ['clone', [5]],
            'resetPermissions'  => ['resetPermissions', []],
        ];
    }

    /**
     * @dataProvider requiresIdProvider
     *
     * @param array<int, mixed> $arguments
     */
    public function testMethodsThatNeedASiteIdThrowWithoutOne(string $method, array $arguments): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->servers(1)->sites()->{$method}(...$arguments);
    }

    /**
     * @return array<string, array{0: string, 1: class-string}>
     */
    public function childResourceProvider(): array
    {
        return [
            'redirects'          => ['redirects', Redirect::class],
            'certificates'       => ['certificates', Certificate::class],
            'repository'         => ['repository', Repository::class],
            'queues'             => ['queues', Queue::class],
            'deployment'         => ['deployment', Deployment::class],
            'app'                => ['app', App::class],
            'environment'        => ['environment', Environment::class],
            'alias'              => ['alias', Alias::class],
            'fastCgi'            => ['fastCgi', FastCgi::class],
            'authUser'           => ['authUser', AuthUser::class],
            'robots'             => ['robots', Robot::class],
            'tenants'            => ['tenants', Tenant::class],
            'monitors'           => ['monitors', Monitors::class],
            'nginxConfiguration' => ['nginxConfiguration', NginxConfiguration::class],
        ];
    }

    /**
     * @dataProvider childResourceProvider
     *
     * @param class-string $expected
     */
    public function testExposesItsChildResources(string $method, string $expected): void
    {
        $this->assertInstanceOf($expected, $this->ploi->servers(1)->sites(2)->{$method}());
    }
}
