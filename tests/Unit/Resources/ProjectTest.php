<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Ploi\Resources\Project;
use Tests\Unit\TestCase;

class ProjectTest extends TestCase
{
    public function testListsProjects(): void
    {
        $this->queue();

        $this->ploi->projects()->get();

        $this->assertRequest('get', 'projects?page=1');
    }

    public function testGetsASingleProject(): void
    {
        $this->queue();

        $this->ploi->projects(1)->get();

        $this->assertRequest('get', 'projects/1');
    }

    public function testCreatesAProject(): void
    {
        $this->queue(['data' => ['id' => 4]]);

        $project = $this->ploi->projects();
        $project->create('Webshop', [1], [2]);

        $this->assertRequest('post', 'projects', [
            'title'   => 'Webshop',
            'servers' => [1],
            'sites'   => [2],
        ]);

        $this->assertSame(4, $project->getId());
    }

    public function testCreateAcceptsExtraOptions(): void
    {
        $this->queue(['data' => ['id' => 4]]);

        $this->ploi->projects()->create('Webshop', [], [], ['description' => 'Our shop']);

        $this->assertRequest('post', 'projects', [
            'title'       => 'Webshop',
            'servers'     => [],
            'sites'       => [],
            'description' => 'Our shop',
        ]);
    }

    public function testUpdatesAProject(): void
    {
        $this->queue();

        $this->ploi->projects(1)->update('Renamed', [1], [2]);

        $this->assertRequest('patch', 'projects/1', [
            'title'   => 'Renamed',
            'servers' => [1],
            'sites'   => [2],
        ]);
    }

    public function testDeletesAProject(): void
    {
        $this->queue();

        $this->ploi->projects(1)->delete();

        $this->assertRequest('delete', 'projects/1');
    }

    public function testSearchesProjects(): void
    {
        $this->queue();

        $this->ploi->projects()->search('shop');

        $this->assertRequest('get', 'projects?search=shop');
    }

    public function testUpdateAndDeleteRequireAnId(): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->projects()->update('Renamed');
    }

    public function testProjectsIsAnAliasForProject(): void
    {
        $this->assertInstanceOf(Project::class, $this->ploi->project());
        $this->assertInstanceOf(Project::class, $this->ploi->projects());
    }
}
