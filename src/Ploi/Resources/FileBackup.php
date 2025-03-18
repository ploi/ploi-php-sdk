<?php

namespace Ploi\Resources;

use Ploi\Http\Response;
use Ploi\Traits\HasPagination;

class FileBackup extends Resource
{
    use HasPagination;

    public function __construct(\Ploi\Ploi $ploi, int $id = null)
    {
        parent::__construct($ploi, $id);

        $this->buildEndpoint();
    }

    public function buildEndpoint(): self
    {
        $this->setEndpoint('backups/file');

        if ($this->getId()) {
            $this->setEndpoint($this->getEndpoint() . '/' . $this->getId());
        }

        if ($this->getAction()) {
            $this->setEndpoint($this->getEndpoint() . '/' . $this->getAction());
        }

        return $this;
    }

    /**
     * Get all file backups
     *
     * @param int|null $id
     * @return Response
     */
    public function get(int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        // Make sure the endpoint is built
        $this->buildEndpoint();

        return (is_null($this->getId()))
            ? $this->page()
            : $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    /**
     * Create a new file backup
     *
     * @param int $backup_configuration The ID of your backup configuration
     * @param int $server The ID of the server
     * @param array $sites An array containing the ID's of the sites to back up
     * @param int $interval Backup interval (0, 10, 20, 30, 40, 50, 60, 120, 240, 480, 720, 1440)
     * @param array $path The paths per site ID to be backed up
     * @param string|null $locations Only used for google-drive driver
     * @param int|null $keep_backup_amount How many backups should be saved
     * @param string|null $custom_name Custom archive name
     * @param string|null $password Password for the ZIP archive
     * @return Response
     */
    public function create(
        int $backup_configuration,
        int $server,
        array $sites,
        int $interval,
        array $path,
        string $locations = null,
        int $keep_backup_amount = null,
        string $custom_name = null,
        string $password = null
    ): Response {
        // Remove the id
        $this->setId(null);

        // Set the options
        $options = [
            'body' => json_encode([
                'backup_configuration' => $backup_configuration,
                'server' => $server,
                'sites' => $sites,
                'interval' => $interval,
                'path' => $path,
                'locations' => $locations,
                'keep_backup_amount' => $keep_backup_amount,
                'custom_name' => $custom_name,
                'password' => $password
            ]),
        ];

        $this->buildEndpoint();

        // Make the request
        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);
    }

    /**
     * Run a file backup
     *
     * @param int|null $id
     * @return Response
     */
    public function run(int $id = null): Response
    {
        $this->setIdOrFail($id);

        $this->setAction('run');
        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'post');
    }

    /**
     * Delete a file backup
     *
     * @param int|null $id
     * @return Response
     */
    public function delete(int $id = null): Response
    {
        $this->setIdOrFail($id);

        $this->buildEndpoint();

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');
    }
}
