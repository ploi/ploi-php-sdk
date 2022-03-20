<?php


namespace Ploi\Resources;


use Ploi\Http\Response;
use Ploi\Traits\HasPagination;

class SshKey extends Resource
{
    use HasPagination;

    private $server;

    public function __construct(Server $server, int $id = null)
    {
        parent::__construct($server->getPloi(), $id);

        $this->setServer($server);

        $this->buildEndpoint();
    }

    public function buildEndpoint(): self
    {
        $this->setEndpoint($this->getServer()->getEndpoint() . '/' . $this->getServer()->getId() . '/ssh-keys');

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

        return (! $this->getId()) 
            ? $this->page()
            : $this->getPloi()->makeAPICall($this->getEndpoint()); 
    }

    public function create(string $name, string $key, ?string $systemUser = null): Response
    {
        $this->setId(null);

        $options = [
            'body' => json_encode(
                [
                'name' => $name,
                'key' => $key,
                'system_user' => $systemUser,
                ]
            ),
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

}
