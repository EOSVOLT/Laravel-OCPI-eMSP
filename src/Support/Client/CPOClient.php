<?php

namespace Ocpi\Support\Client;

use Ocpi\Modules\Cdrs\Client\Resource as CdrsResource;
use Ocpi\Modules\Commands\Client\Resource as CommandsResource;
use Ocpi\Modules\Credentials\Client\Resource as CredentialsResource;
use Ocpi\Modules\Locations\Client\Resource as LocationsResource;
use Ocpi\Modules\Sessions\Client\Resource as SessionsResource;
use Ocpi\Modules\Versions\Client\Resource as VersionsResource;
use Saloon\Http\Auth\TokenAuthenticator;

class CPOClient extends Client
{
    public function resolveBaseUrl(): string
    {
        return match ($this->module) {
            'versions.information' => $this->party?->url,
            'versions.details' => $this->party?->version_url,
            default => $this->party?->endpoints[$this->module] ?? '',
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

    public function sessions(): SessionsResource
    {
        return new SessionsResource($this);
    }

    public function versions(): VersionsResource
    {
        return new VersionsResource($this);
    }

    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator($this->party?->encoded_client_token, 'Token');
    }
}