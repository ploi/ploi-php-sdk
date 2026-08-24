<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Tests\Unit\TestCase;

class DatabaseBackupTest extends TestCase
{
    public function testListsDatabaseBackups(): void
    {
        $this->queue();

        $this->ploi->servers(1)->databases(2)->backups()->get();

        $this->assertRequest('get', 'backups/database?page=1');
    }

    public function testGetsASingleDatabaseBackup(): void
    {
        $this->queue();

        $this->ploi->servers(1)->databases(2)->backups(3)->get();

        $this->assertRequest('get', 'backups/database/3');
    }

    public function testCreatesABackupForTheParentDatabase(): void
    {
        $this->queue(['data' => ['id' => 3]]);

        $backup = $this->ploi->servers(1)->databases(2)->backups();
        $backup->create(24, 5);

        $this->assertRequest('post', 'backups/database', [
            'backup_configuration' => 5,
            'server'               => 1,
            'databases'            => [2],
            'interval'             => 24,
            'table_exclusions'     => null,
            'locations'            => null,
            'path'                 => null,
            'keep_backup_amount'   => null,
            'custom_name'          => null,
            'password'             => null,
            'deleteOnFail'         => null,
        ]);

        $this->assertSame(3, $backup->getId());
    }

    public function testCreatesABackupWithExplicitDatabases(): void
    {
        $this->queue(['data' => ['id' => 3]]);

        $this->ploi->servers(1)->databases(2)->backups()->create(
            12,
            5,
            [7, 8],
            'logs',
            'local',
            '/backups',
            10,
            'nightly',
            'secret',
            true
        );

        $this->assertRequest('post', 'backups/database', [
            'backup_configuration' => 5,
            'server'               => 1,
            'databases'            => [7, 8],
            'interval'             => 12,
            'table_exclusions'     => 'logs',
            'locations'            => 'local',
            'path'                 => '/backups',
            'keep_backup_amount'   => 10,
            'custom_name'          => 'nightly',
            'password'             => 'secret',
            'deleteOnFail'         => true,
        ]);
    }

    public function testTogglesABackup(): void
    {
        $this->queue();

        $this->ploi->servers(1)->databases(2)->backups(3)->toggle();

        $this->assertRequest('patch', 'backups/database/3/toggle');
    }

    public function testDeletesABackup(): void
    {
        $this->queue();

        $this->ploi->servers(1)->databases(2)->backups(3)->delete();

        $this->assertRequest('delete', 'backups/database/3');
    }

    public function testDeleteRequiresAnId(): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->servers(1)->databases(2)->backups()->delete();
    }
}
