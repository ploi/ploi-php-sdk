<?php
declare(strict_types=1);

namespace Ploi\Resources;

use Ploi\Http\Response;
use Ploi\Traits\HasPagination;

class DatabaseBackup extends Resource
{
    use HasPagination;

    public function __construct(Server $server, Database $database, ?int $id = null)
    {
        parent::__construct($server->getPloi(), $id);

        $this->setServer($server);

        $this->setDatabase($database);

        $this->buildEndpoint();
    }

    public function buildEndpoint(): self
    {
        $this->setEndpoint('backups/database');

        if ($this->getId()) {
            $this->setEndpoint($this->getEndpoint() . '/' . $this->getId());
        }

        return $this;
    }

    public function get(?int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        $this->buildEndpoint();

        return (is_null($this->getId()))
            ? $this->page()
            : $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    public function create(
        int $interval,
        int $backup_configuration,
        ?array $databases = null,
        ?string $table_exclusions = null,
        ?string $locations = null,
        ?string $path = null,
        ?int $keep_backup_amount = null,
        ?string $custom_name = null,
        ?string $password = null
    ): Response {
        $this->setId(null);

        $options = [
            'body' => json_encode([
                'backup_configuration' => $backup_configuration,
                'server' => $this->getServer()->getId(),
                'databases' => $databases ?? [$this->getDatabase()->getId()],
                'interval' => $interval,
                'table_exclusions' => $table_exclusions,
                'locations' => $locations,
                'path' => $path,
                'keep_backup_amount' => $keep_backup_amount,
                'custom_name' => $custom_name,
                'password' => $password,
            ]),
        ];

        $this->buildEndpoint();

        $response = $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);

        $this->setId($response->getJson()->data->id);

        return $response;
    }

    public function delete(?int $id = null): Response
    {
        $this->setIdOrFail($id);

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');
    }

    public function toggle(?int $id = null): Response
    {
        $this->setIdOrFail($id);

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint() . '/toggle', 'patch');
    }
}
