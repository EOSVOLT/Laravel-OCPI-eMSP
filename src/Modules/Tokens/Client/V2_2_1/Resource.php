<?php

namespace Ocpi\Modules\Tokens\Client\V2_2_1;

use Ocpi\Models\PartyRole;
use Ocpi\Modules\Locations\Enums\TokenType;
use Ocpi\Modules\Tokens\Objects\CommandToken;
use Ocpi\Modules\Tokens\Resources\CommandTokenResource;
use Ocpi\Support\Client\Resource as OcpiResource;
use Ocpi\Support\Traits\DateFormat;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Throwable;

class Resource extends OcpiResource
{
    use DateFormat;

    /**
     * @throws FatalRequestException
     * @throws Throwable
     * @throws RequestException
     */
    public function get(
        PartyRole $partyRole,
        CommandToken $commandTokens,
        ?TokenType $type = null,
    ): void {
        $query = array_filter(['type' => $type?->value]);
        $this->requestGetSend(
            implode('/', [$partyRole->country_code, $partyRole->code, $commandTokens->getTokenUid()]),
            query: $query
        );
    }

    /**
     * @throws FatalRequestException
     * @throws Throwable
     * @throws RequestException
     */
    public function put(PartyRole $partyRole, CommandToken $commandToken): void
    {
        $this->requestPutSend(
            new CommandTokenResource($commandToken)->toArray(),
            implode('/', [$partyRole->country_code, $partyRole->code, $commandToken->getTokenUid()])
        );
    }

    /**
     * @throws FatalRequestException
     * @throws Throwable
     * @throws RequestException
     */
    public function patch(PartyRole $partyRole, CommandToken $commandToken, array $fields): void
    {
        $payload = array_filter($commandToken->toArray(), function ($value,$key) use ($fields) {
            return in_array($key, $fields);
        }, ARRAY_FILTER_USE_BOTH);
        if (empty($payload)) {
            return;
        }
        $this->requestPatchSend(
            $payload,
            implode('/', [$partyRole->country_code, $partyRole->code, $commandToken->getTokenUid()])
        );
    }
}
