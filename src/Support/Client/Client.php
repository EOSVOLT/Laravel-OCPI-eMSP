<?php

namespace Ocpi\Support\Client;

use Exception;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Ocpi\Models\Party;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Response;
use Saloon\Traits\Plugins\AcceptsJson;

class Client extends Connector
{
    use AcceptsJson;

    public function __construct(
        protected readonly Party $party,
        protected string $module,
    ) {
        Context::add('trace_id', Str::uuid()->toString());
        Context::add('party_code', $party?->code);
    }

    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator($this->party?->encoded_server_token, 'Token');
    }

    protected function defaultConfig(): array
    {
        return [];
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    public function resolveBaseUrl(): string
    {
        return match ($this->module) {
            'versions.information' => $this->party?->url,
            'versions.details' => $this->party?->version_url,
            default => $this->party?->endpoints[$this->module] ?? '',
        };
    }

    public function hasRequestFailed(Response $response): ?bool
    {
        if ($response->header('Content-Type') === 'application/json') {
            try {
                $responseObject = $response->object();

                return ($responseObject?->status_code ?? 2000) >= 2000;
            } catch (Exception $e) {
                return true;
            }
        }

        return true;
    }

    /***
     * Methods.
     ***/

    public function module(string $module): void
    {
        $this->module = $module;
    }
}
