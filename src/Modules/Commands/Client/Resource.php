<?php

namespace Ocpi\Modules\Commands\Client;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Log;
use Ocpi\Models\Commands\Command;
use Ocpi\Models\Commands\Enums\CommandResponseType;
use Ocpi\Models\Commands\Enums\CommandType;
use Ocpi\Models\PartyRole;
use Ocpi\Modules\Commands\Events;
use Ocpi\Support\Client\Resource as OcpiResource;

class Resource extends OcpiResource
{
    public function reserveNow(PartyRole $partyRole, array $payload): void
    {
        try {
            DB::connection(config('ocpi.database.connection'))->beginTransaction();

            $command = Command::create([
                'party_role_id' => $partyRole->id,
                'type' => CommandType::RESERVE_NOW,
            ]);

            $payload['response_url'] = $this->responseUrl($partyRole, $command);

            $command->payload = $payload;
            $command->save();

            DB::connection(config('ocpi.database.connection'))->commit();

            $response = $this->requestPostSend(
                payload: $command->payload->toArray(),
                endpoint: $command->type->name,
            );

            $commandResponseType = CommandResponseType::fromName($response);
            if (! $commandResponseType) {
                Log::channel('ocpi')->error('Unknown CommandResponseType '.json_encode($response));
                throw new Exception('Unknown CommandResponseType '.json_encode($response));
            }

            $command->response = $commandResponseType->name;
            $command->save();

            if ($commandResponseType === CommandResponseType::ACCEPTED) {
                Events\CommandResponseAccepted::dispatch($partyRole->id, $command->id, $command->type->name);
            } else {
                Events\CommandResponseError::dispatch($partyRole->id, $command->id, $command->type->name, $payload);

                throw new Exception('Command not accepted');
            }
        } catch (Exception $e) {
            DB::connection(config('ocpi.database.connection'))->rollBack();
            throw $e;
        }
    }

    public function cancelReservation(PartyRole $partyRole, array $payload): void
    {
        try {
            DB::connection(config('ocpi.database.connection'))->beginTransaction();

            $command = Command::create([
                'party_role_id' => $partyRole->id,
                'type' => CommandType::CANCEL_RESERVATION,
            ]);

            $payload['response_url'] = $this->responseUrl($partyRole, $command);

            $command->payload = $payload;
            $command->save();

            DB::connection(config('ocpi.database.connection'))->commit();

            $response = $this->requestPostSend(
                payload: $command->payload->toArray(),
                endpoint: $command->type->name,
            );

            $commandResponseType = CommandResponseType::fromName($response);
            if (! $commandResponseType) {
                Log::channel('ocpi')->error('Unknown CommandResponseType '.json_encode($response));
                throw new Exception('Unknown CommandResponseType '.json_encode($response));
            }

            $command->response = $commandResponseType->name;
            $command->save();

            if ($commandResponseType === CommandResponseType::ACCEPTED) {
                Events\CommandResponseAccepted::dispatch($partyRole->id, $command->id, $command->type->name);
            } else {
                Events\CommandResponseError::dispatch($partyRole->id, $command->id, $command->type->name, $payload);

                throw new Exception('Command not accepted');
            }
        } catch (Exception $e) {
            DB::connection(config('ocpi.database.connection'))->rollBack();
            throw $e;
        }
    }

    private function responseUrl(PartyRole $partyRole, Command $command): string
    {
        return (config('ocpi.server.enabled', false) === true)
            ? route('ocpi.emsp.'.Str::replace('.', '_', $partyRole?->party?->version).'.commands.post', [
                'type' => $command->type->name,
                'id' => $command->id,
            ])
            : config('ocpi.client.server.url').'/'.$partyRole?->party?->version.'/commands/'.$command->type->name.'/'.$command->id;
    }
}
