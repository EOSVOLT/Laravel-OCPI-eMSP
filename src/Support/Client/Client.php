<?php

namespace Ocpi\Support\Client;

use Exception;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Ocpi\Models\Party;
use Ocpi\Models\PartyToken;
use Ocpi\Modules\Cdrs\Client\Resource as CdrsResource;
use Ocpi\Modules\Commands\Client\Resource as CommandsResource;
use Ocpi\Modules\Credentials\Client\Resource as CredentialsResource;
use Ocpi\Modules\Locations\Client\V2_2_1\Resource as LocationsResource;
use Ocpi\Modules\Sessions\Client\Resource as SessionsResource;
use Ocpi\Modules\Versions\Client\Resource as VersionsResource;
use Ocpi\Modules\Tariffs\Client\V2_2_1\Resource as TariffResource;
use Ocpi\Support\Client\Middlewares\LogRequest;
use Ocpi\Support\Client\Middlewares\LogResponse;
use Ocpi\Support\Enums\InterfaceRole;
use Ocpi\Support\Enums\Role;
use Ocpi\Support\Helpers\GeneratorHelper;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Response;
use Saloon\Traits\Plugins\AcceptsJson;

class Client extends Connector
{
    use AcceptsJson;

    protected InterfaceRole $interfaceRole;
    public function __construct(
        protected readonly Party $party,
        protected readonly PartyToken $partyToken,
        protected string $module,
        protected ?Role $role = null,
    ) {
        Context::add('trace_id', Str::uuid()->toString());
        Context::add('party_code', $party?->code);

        $this->middleware()->onRequest(new LogRequest);
        $this->middleware()->onResponse(new LogResponse);
    }

    public function hasRequestFailed(Response $response): ?bool
    {
        if (true === $response->isJson()) {
            try {
                $responseObject = $response->object();

                return ($responseObject?->status_code ?? 2000) >= 2000;
            } catch (Exception $e) {
                return true;
            }
        }

        return true;
    }

    public function resolveBaseUrl(): string
    {
        return match ($this->module) {
            'versions.information' => $this->party?->roles->where('role', $this->role->value)->first()->url,
            'versions.details' => $this->party?->version_url,
            default => $this->party?->endpoints[$this->module][$this->interfaceRole->value] ?? '',
        };
    }


    /***
     * Methods.
     ***/

    public function module(string $module): void
    {
        $this->module = $module;
    }

    /***
     * Resources.
     ***/

    public function cdrs(): CdrsResource
    {
        return new CdrsResource($this);
    }

    public function commands(): CommandsResource
    {
        return new CommandsResource($this);
    }

    public function credentials(): CredentialsResource
    {
        return new CredentialsResource($this);
    }

    public function locations(): LocationsResource
    {
        return new LocationsResource($this);
    }

    public function tariffs(): TariffResource
    {
        return new TariffResource($this);
    }

    public function sessions(): SessionsResource
    {
        return new SessionsResource($this);
    }

    public function versions(): VersionsResource
    {
        return new VersionsResource($this);
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
        return [
            'timeout' => 120, // 30 seconds request timeout
            'connect_timeout' => 60, // 10 seconds connection timeout
        ];
    }

    protected function defaultAuth(): TokenAuthenticator
    {
        $token = GeneratorHelper::encodeToken($this->partyToken->token, $this->party->version);
        return new TokenAuthenticator($token, 'Token');
    }
}