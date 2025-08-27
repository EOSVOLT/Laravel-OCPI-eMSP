<?php

namespace Ocpi\Support\Client;

use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Ocpi\Models\Party;
use Ocpi\Support\Client\Middlewares\LogRequest;
use Ocpi\Support\Client\Middlewares\LogResponse;
use Saloon\Http\Connector;
use Saloon\Http\Response;
use Saloon\Traits\Plugins\AcceptsJson;

abstract class Client extends Connector
{
    use AcceptsJson;

    public function __construct(
        protected readonly Party $party,
        protected string $module,
    ) {
        Context::add('trace_id', Str::uuid()->toString());
        Context::add('party_code', $party?->code);

        $this->middleware()->onRequest(new LogRequest);
        $this->middleware()->onResponse(new LogResponse);
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    protected function defaultConfig(): array
    {
        return [];
    }

    public function hasRequestFailed(Response $response): ?bool
    {
        if (in_array('application/json', [$response->header('Content-Type'), $response->header('content-type')])) {
            try {
                $responseObject = $response->object();

                return ($responseObject?->status_code ?? 2000) >= 2000;
            } catch (Exception $e) {
                return true;
            }
        }

        return true;
    }
}