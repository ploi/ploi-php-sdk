<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Ploi\Ploi;
use Ploi\Resources\Database;
use Ploi\Resources\Server;
use Ploi\Resources\Site;
use Tests\Unit\TestCase;

/**
 * Covers the behaviour every resource inherits from the abstract base class.
 */
class ResourceTest extends TestCase
{
    public function testHoldsTheIdItWasConstructedWith(): void
    {
        $this->assertSame(3, $this->ploi->servers(3)->getId());
        $this->assertNull($this->ploi->servers()->getId());
    }

    public function testSetIdAcceptsNullToClearIt(): void
    {
        $server = $this->ploi->servers(3);

        $this->assertNull($server->setId()->getId());
    }

    public function testSetIdOrFailUsesTheGivenId(): void
    {
        $server = $this->ploi->servers();

        $this->assertSame(5, $server->setIdOrFail(5)->getId());
    }

    public function testSetIdOrFailKeepsAnAlreadySetId(): void
    {
        $server = $this->ploi->servers(5);

        $this->assertSame(5, $server->setIdOrFail()->getId());
    }

    public function testSetIdOrFailThrowsWithoutAnId(): void
    {
        $this->expectException(RequiresId::class);
        $this->expectExceptionMessage(Server::class);

        $this->ploi->servers()->setIdOrFail();
    }

    public function testExposesThePloiInstance(): void
    {
        $this->assertInstanceOf(Ploi::class, $this->ploi->servers()->getPloi());
    }

    public function testChildResourcesKeepAReferenceToTheirParents(): void
    {
        $site = $this->ploi->servers(1)->sites(2);

        $this->assertInstanceOf(Server::class, $site->getServer());
        $this->assertSame(1, $site->getServer()->getId());

        $certificate = $site->certificates(3);

        $this->assertInstanceOf(Site::class, $certificate->getSite());
        $this->assertSame(2, $certificate->getSite()->getId());
    }

    public function testDatabaseChildrenKeepAReferenceToTheDatabase(): void
    {
        $user = $this->ploi->servers(1)->databases(2)->users(3);

        $this->assertInstanceOf(Database::class, $user->getDatabase());
        $this->assertSame(2, $user->getDatabase()->getId());
    }

    public function testEndpointCanBeReadAndWritten(): void
    {
        $server = $this->ploi->servers();

        $this->assertSame('servers', $server->getEndpoint());
        $this->assertSame('something-else', $server->setEndpoint('something-else')->getEndpoint());
    }

    public function testActionCanBeReadAndWritten(): void
    {
        $server = $this->ploi->servers();

        $this->assertNull($server->getAction());
        $this->assertSame('forget', $server->setAction('forget')->getAction());
    }
}
