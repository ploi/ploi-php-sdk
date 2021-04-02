<?php

namespace Ploi\Resources;

use Ploi\Exceptions\Resource\Script\InvalidUser;
use stdClass;
use Ploi\Ploi;
use Ploi\Exceptions\Resource\RequiresId;

class Script extends Resource
{
    private $endpoint = 'scripts';

    private $availableUsers = [
        'ploi',
        'root',
    ];

    public function __construct(Ploi $ploi = null, int $id = null)
    {
        parent::__construct($ploi, $id);

        $this->setEndpoint($this->endpoint);
    }

    public function get(int $id = null): stdClass
    {
        if ($id) {
            $this->setId($id);
        }

        if ($this->getId()) {
            $this->setEndpoint($this->getEndpoint() . '/' . $this->getId());
        }

        $response = $this->getPloi()->makeAPICall($this->getEndpoint());

        return $response->getJson();
    }

    public function create(
        string $label,
        string $user,
        string $content
    ): stdClass
    {
        $this->setId(null);

        if (!in_array($user, $this->availableUsers)) {
            throw new InvalidUser(
                'User not valid, available users: ' . implode(', ', $this->availableUsers)
            );
        }

        $options = [
            'body' => json_encode([
                'label' => $label,
                'user' => $user,
                'content' => $content,
            ]),
        ];

        $response = $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);

        $this->setId($response->getJson()->data->id);

        return $response->getJson();
    }

    public function delete(int $id = null): bool
    {
        if ($id) {
            $this->setId($id);
        }
        if (!$this->getId()) {
            throw new RequiresId('Script ID is required');
        }

        $this->setEndpoint($this->getEndpoint() . '/' . $this->getId());

        $response = $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');

        return $response->getResponse()->getStatusCode() === 200;
    }

    public function run(int $id = null, array $serverIds = []): stdClass
    {
        if ($id) {
            $this->setId($id);
        }
        if (!$this->getId()) {
            throw new RequiresId('Script ID is required');
        }
        if (!count($serverIds)) {
            throw new RequiresId('Server IDs are required');
        }
        $options = [
            'body' => json_encode([
                'servers' => $serverIds
            ])
        ];

        $this->setEndpoint($this->getEndpoint() . '/' . $this->getId() . '/run');

        $response = $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);

        return $response->getJson();
    }
}
