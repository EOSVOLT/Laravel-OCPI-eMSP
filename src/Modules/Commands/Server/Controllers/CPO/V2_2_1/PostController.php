<?php

namespace Ocpi\Modules\Commands\Server\Controllers\CPO\V2_2_1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;
use Ocpi\Models\Commands\Command;
use Ocpi\Models\Commands\Enums\CommandType;
use Ocpi\Models\PartyRole;
use Ocpi\Modules\Commands\Events\RemoteStartTransaction;
use Ocpi\Modules\Commands\Factories\CommandTokenFactory;
use Ocpi\Modules\Locations\Enums\TokenType;
use Ocpi\Support\Server\Controllers\Controller;

class PostController extends Controller
{
    public function __invoke(Request $request, CommandType $commandType): JsonResponse
    {
        try {
            //@todo request class with validation the request for each commandType

            $token = CommandTokenFactory::fromArray($request->input('token'));
            if (TokenType::RFID === $token->getType()) {
                return $this->ocpiServerErrorResponse(statusMessage: 'RFID is not support yet.');
            }

            $partyRole = PartyRole::query()
                ->where('code', $token->getPartyCode())
                ->where('country_code', $token->getCountryCode())
                ->first();

            $payload = $request->validated();
            $command = Command::query()->create([
                'party_role_id' => $partyRole->id,
                'type' => $commandType,
                'payload' => $payload,
            ]);
            match ($commandType) {
                CommandType::START_SESSION => RemoteStartTransaction::dispatch($partyRole->id, $command->id, $command->type->name),
                CommandType::STOP_SESSION => throw new \Exception('To be implemented'),
                CommandType::CANCEL_RESERVATION => throw new \Exception('To be implemented'),
                CommandType::RESERVE_NOW => throw new \Exception('To be implemented'),
                CommandType::UNLOCK_CONNECTOR => throw new \Exception('To be implemented')
            };

            return $this->ocpiCommandAcceptedResponse();
        } catch (Exception $e) {
            Log::channel('ocpi')->error($e->getMessage());
            return $this->ocpiServerErrorResponse();
        }
    }
}
