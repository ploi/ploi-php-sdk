<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Tests\Unit\TestCase;

class AliasTest extends TestCase
{
    public function testGetsAliases(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->alias()->get();

        $this->assertRequest('get', 'servers/1/sites/2/aliases');
    }

    public function testCreatesAliases(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->alias()->create(['www.example.com', 'example.nl']);

        $this->assertRequest('post', 'servers/1/sites/2/aliases', [
            'aliases' => ['www.example.com', 'example.nl'],
        ]);
    }

    public function testDeletesAnAlias(): void
    {
        $this->queue();

        $this->ploi->servers(1)->sites(2)->alias()->delete('www.example.com');

        $this->assertRequest('delete', 'servers/1/sites/2/aliases/www.example.com');
    }
}
