<?php

namespace Ocpi\Support\Client\Requests;

use Saloon\Http\Request;

class BaseRequest extends Request
{
    protected ?string $endpoint = null;

    protected ?array $payload = null;

    public function resolveEndpoint(): string
    {
        return $this->endpoint ?? '';
    }

    /***
     * Methods.
     ***/

    public function endpoint(?string $endpoint = null): BaseRequest
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function withPayload(?array $payload = null): BaseRequest
    {
        $this->payload = $payload ?? [];

        return $this;
    }

    public function withQuery(?array $query = null): BaseRequest
    {
        foreach (($query ?? []) as $key => $value) {
            $this->query()->add($key, $value);
        }

        return $this;
    }
}
