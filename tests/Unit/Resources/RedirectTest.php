<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Tests\Unit\TestCase;

class RedirectTest extends TestCase
{
    public function testListsRedirects(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->redirects()->get();

        $this->assertRequest('get', 'servers/1/sites/2/redirects?page=1');
    }

    public function testGetsASingleRedirect(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->redirects(3)->get();

        $this->assertRequest('get', 'servers/1/sites/2/redirects/3');
    }

    public function testCreatesARedirect(): void
    {
        $this->queue(['data' => ['id' => 3]]);

        $redirect = $this->ploi->servers(1)->sites(2)->redirects();
        $redirect->create('/old', '/new');

        $this->assertRequest('post', 'servers/1/sites/2/redirects', [
            'redirect_from' => '/old',
            'redirect_to'   => '/new',
            'type'          => 'redirect',
        ]);

        $this->assertSame(3, $redirect->getId());
    }

    public function testCreatesAPermanentRedirect(): void
    {
        $this->queue(['data' => ['id' => 3]]);

        $this->ploi->servers(1)->sites(2)->redirects()->create('/old', '/new', 'permanent');

        $this->assertRequest('post', 'servers/1/sites/2/redirects', [
            'redirect_from' => '/old',
            'redirect_to'   => '/new',
            'type'          => 'permanent',
        ]);
    }

    public function testDeletesARedirect(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->redirects(3)->delete();

        $this->assertRequest('delete', 'servers/1/sites/2/redirects/3');
    }

    public function testDeleteRequiresAnId(): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->servers(1)->sites(2)->redirects()->delete();
    }
}
