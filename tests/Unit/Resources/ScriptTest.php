<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Tests\Unit\TestCase;

class ScriptTest extends TestCase
{
    public function testListsScripts(): void
    {
        $this->queue();

        $this->ploi->scripts()->get();

        $this->assertRequest('get', 'scripts?page=1');
    }

    public function testGetsASingleScript(): void
    {
        $this->queue();

        $this->ploi->scripts(1)->get();

        $this->assertRequest('get', 'scripts/1');
    }

    public function testCreatesAScript(): void
    {
        $this->queue(['data' => ['id' => 4]]);

        $script = $this->ploi->scripts();
        $script->create('Restart nginx', 'root', 'service nginx restart');

        $this->assertRequest('post', 'scripts', [
            'label'   => 'Restart nginx',
            'user'    => 'root',
            'content' => 'service nginx restart',
        ]);

        $this->assertSame(4, $script->getId());
    }

    public function testDeletesAScript(): void
    {
        $this->queue();

        $this->ploi->scripts(1)->delete();

        $this->assertRequest('delete', 'scripts/1');
    }

    public function testRunsAScriptOnServers(): void
    {
        $this->queue();

        $this->ploi->scripts(1)->run(null, [7, 8]);

        $this->assertRequest('post', 'scripts/1/run', ['servers' => [7, 8]]);
    }

    public function testRunRequiresAScriptId(): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->scripts()->run(null, [7]);
    }

    public function testRunRequiresServerIds(): void
    {
        $this->expectException(RequiresId::class);
        $this->expectExceptionMessage('Server IDs are required');

        $this->ploi->scripts(1)->run();
    }

    public function testDeleteRequiresAnId(): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->scripts()->delete();
    }
}
