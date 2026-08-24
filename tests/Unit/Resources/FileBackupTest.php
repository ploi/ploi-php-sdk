<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use Ploi\Exceptions\Resource\RequiresId;
use Ploi\Resources\FileBackup;
use Tests\Unit\TestCase;

class FileBackupTest extends TestCase
{
    public function testListsFileBackups(): void
    {
        $this->queue();

        $this->ploi->fileBackups()->get();

        $this->assertRequest('get', 'backups/file?page=1');
    }

    public function testGetsASingleFileBackup(): void
    {
        $this->queue();

        $this->ploi->fileBackups(1)->get();

        $this->assertRequest('get', 'backups/file/1');
    }

    public function testCreatesAFileBackup(): void
    {
        $this->queue();

        $this->ploi->fileBackups()->create(5, 1, [2], 24, ['/home/ploi']);

        $this->assertRequest('post', 'backups/file', [
            'backup_configuration' => 5,
            'server'               => 1,
            'sites'                => [2],
            'interval'             => 24,
            'path'                 => ['/home/ploi'],
            'locations'            => null,
            'keep_backup_amount'   => null,
            'custom_name'          => null,
            'password'             => null,
            'deleteOnFail'         => null,
        ]);
    }

    public function testCreatesAFileBackupWithEveryOption(): void
    {
        $this->queue();

        $this->ploi->fileBackups()->create(5, 1, [2], 12, ['/var/www'], 'local', 7, 'nightly', 'secret', true);

        $this->assertRequest('post', 'backups/file', [
            'backup_configuration' => 5,
            'server'               => 1,
            'sites'                => [2],
            'interval'             => 12,
            'path'                 => ['/var/www'],
            'locations'            => 'local',
            'keep_backup_amount'   => 7,
            'custom_name'          => 'nightly',
            'password'             => 'secret',
            'deleteOnFail'         => true,
        ]);
    }

    public function testRunsAFileBackup(): void
    {
        $this->queue();

        $this->ploi->fileBackups(1)->run();

        $this->assertRequest('post', 'backups/file/1/run');
    }

    public function testDeletesAFileBackup(): void
    {
        $this->queue();

        $this->ploi->fileBackups(1)->delete();

        $this->assertRequest('delete', 'backups/file/1');
    }

    public function testRunAndDeleteRequireAnId(): void
    {
        $this->expectException(RequiresId::class);

        $this->ploi->fileBackups()->run();
    }

    public function testFileBackupsIsAnAliasForFileBackup(): void
    {
        $this->assertInstanceOf(FileBackup::class, $this->ploi->fileBackup());
        $this->assertInstanceOf(FileBackup::class, $this->ploi->fileBackups());
    }
}
