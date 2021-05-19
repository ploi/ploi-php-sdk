<?php

namespace Ploi\Resources;

use Ploi\Ploi;
use Ploi\Http\Response;
use Ploi\Exceptions\Resource\RequiresId;
use Ploi\Traits\HasPagination;

class Script extends Resource
{
    use HasPagination;

    private $endpoint = 'scripts';

    public function __construct(Ploi $ploi = null, int $id = null)
    {
        parent::__construct($ploi, $id);

        $this->setEndpoint($this->endpoint);
    }

    public function get(int $id = null): Response
    {
        if ($id) {
            $this->setId($id);
        }

        if ($this->getId()) {
            $this->setEndpoint($this->getEndpoint() . '/' . $this->getId());
        }

        return $this->getPloi()->makeAPICall($this->getEndpoint());
    }

    public function create(
        string $label,
        string $user,
        string $content
    ): Response
    {
        $this->setId(null);

        $options = [
            'body' => json_encode([
                'label' => $label,
                'user' => $user,
                'content' => $content,
            ]),
        ];

        $response = $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);

        $this->setId($response->getJson()->data->id);

        return $response;
    }

    public function delete(int $id = null): Response
    {
        $this->setIdOrFail($id);

        $this->setEndpoint($this->getEndpoint() . '/' . $this->getId());

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'delete');
    }

    public function run(int $id = null, array $serverIds = []): Response
    {
        $this->setIdOrFail($id);

        if (!count($serverIds)) {
            throw new RequiresId('Server IDs are required');
        }

        $options = [
            'body' => json_encode([
                'servers' => $serverIds
            ])
        ];

        $this->setEndpoint($this->getEndpoint() . '/' . $this->getId() . '/run');

        return $this->getPloi()->makeAPICall($this->getEndpoint(), 'post', $options);
    }
}
