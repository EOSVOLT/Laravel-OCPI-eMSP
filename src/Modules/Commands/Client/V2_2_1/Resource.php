<?php

namespace Ocpi\Modules\Commands\Client\V2_2_1;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Ocpi\Models\Commands\Command;
use Ocpi\Models\PartyRole;
use Ocpi\Models\Sessions\Session;
use Ocpi\Modules\Commands\Enums\CommandResponseType;
use Ocpi\Modules\Commands\Enums\CommandType;
use Ocpi\Modules\Commands\Events;
use Ocpi\Modules\DTOs\RemoteStartTransactionRequestDTO;
use Ocpi\Modules\DTOs\RemoteStopTransactionRequestDTO;
use Ocpi\Modules\Tokens\Objects\CommandToken;
use Ocpi\Support\Client\Resource as OcpiResource;
use Ocpi\Support\Enums\InterfaceRole;

class Resource extends OcpiResource
{

    public function remoteStartTransaction(
        CommandToken $commandToken,
        string $locationId,
        ?string $evseUid = null,
        ?string $connectorId = null,
    ): Command {
        $command = Command::query()->create([
            'party_role_id' => $commandToken->getPartyRoleId(),
            'type' => CommandType::START_SESSION,
            'interface_role' => InterfaceRole::SENDER,
        ]);
        $dto = new RemoteStartTransactionRequestDTO(
            implode('/', [config('app.url'), $command->id]),
            $commandToken,
            $locationId,
            $evseUid,
            $connectorId
        );
        Log::channel('ocpi')->info('OCPI:COMMAND:START_SESSION:REQUEST: ' . $command->id, $dto->toArray());
        $command->update(['payload' => $dto->toArray()]);
        $response = $this->commandRequestSend(
            $dto->toArray(),
            '/' . CommandType::START_SESSION->value
        );
        Log::channel('ocpi')->info('OCPI:COMMAND:START_SESSION:RESPONSE: ' . $command->id, $response->toArray());
        $command->update(['response' => $response->getResult()]);
        return $command->refresh();
    }

    public function remoteStopTransaction(Session $session): Command
    {
        $command = Command::query()->create([
            'party_role_id' => $session->party_role_id,
            'type' => CommandType::STOP_SESSION,
            'interface_role' => InterfaceRole::SENDER,
        ]);
        $dto = new RemoteStopTransactionRequestDTO(
            implode('/', [config('app.url'), $command->id]),
            $session->id
        );
        Log::channel('ocpi')->info('OCPI:COMMAND:STOP_SESSION:REQUEST: ' . $command->id, $dto->toArray());
        $command->update(['payload' => $dto->toArray()]);
        $response = $this->commandRequestSend(
            $dto->toArray(),
            '/' . CommandType::STOP_SESSION->value
        );
        Log::channel('ocpi')->info('OCPI:COMMAND:STOP_SESSION:RESPONSE: ' . $command->id, $response->toArray());
        $command->update(['response' => $response->getResult()]);
        return $command->refresh();
    }

    public function reserveNow(PartyRole $partyRole, array $payload): void
    {
        $command = DB::connection(config('ocpi.database.connection'))
            ->transaction(function () use ($partyRole, $payload) {
                $command = Command::create([
                    'party_role_id' => $partyRole->id,
                    'type' => CommandType::RESERVE_NOW,
                ]);

                $payload['response_url'] = $this->responseUrl($partyRole, $command);

                $command->payload = $payload;
                $command->save();

                return $command;
            });

        $response = $this->requestPostSend(
            payload: $command->payload->toArray(),
            endpoint: $command->type->name,
        );

        $commandResponseType = CommandResponseType::fromName($response);
        if (!$commandResponseType) {
            Log::channel('ocpi')->error('Unknown CommandResponseType ' . json_encode($response));
            throw new Exception('Unknown CommandResponseType ' . json_encode($response));
        }

        $command->response = $commandResponseType->name;
        $command->save();

        if ($commandResponseType === CommandResponseType::ACCEPTED) {
            Events\CommandResponseAccepted::dispatch($partyRole->id, $command->id, $command->type->name);
        } else {
            Events\CommandResponseError::dispatch(
                $partyRole->id,
                $command->id,
                $command->type->name,
                $command->payload
            );
        }
    }

    public function cancelReservation(PartyRole $partyRole, array $payload): void
    {
        $command = DB::connection(config('ocpi.database.connection'))
            ->transaction(function () use ($partyRole, $payload) {
                $command = Command::create([
                    'party_role_id' => $partyRole->id,
                    'type' => CommandType::CANCEL_RESERVATION,
                ]);

                $payload['response_url'] = $this->responseUrl($partyRole, $command);

                $command->payload = $payload;
                $command->save();

                return $command;
            });

        $response = $this->requestPostSend(
            payload: $command->payload->toArray(),
            endpoint: $command->type->name,
        );

        $commandResponseType = CommandResponseType::fromName($response);
        if (!$commandResponseType) {
            Log::channel('ocpi')->error('Unknown CommandResponseType ' . json_encode($response));
            throw new Exception('Unknown CommandResponseType ' . json_encode($response));
        }

        $command->response = $commandResponseType->name;
        $command->save();

        if ($commandResponseType === CommandResponseType::ACCEPTED) {
            Events\CommandResponseAccepted::dispatch($partyRole->id, $command->id, $command->type->name);
        } else {
            Events\CommandResponseError::dispatch(
                $partyRole->id,
                $command->id,
                $command->type->name,
                $command->payload
            );
        }
    }

    private function responseUrl(PartyRole $partyRole, Command $command): string
    {
        return (config('ocpi.server.enabled', false) === true)
            ? route('ocpi.emsp.' . Str::replace('.', '_', $partyRole?->party?->version) . '.commands.post', [
                'type' => $command->type->name,
                'id' => $command->id,
            ])
            : config(
                'ocpi.client.server.url'
            ) . '/emsp/' . $partyRole?->party?->version . '/commands/' . $command->type->name . '/' . $command->id;
    }
}
