<?php

namespace Ocpi\Modules\Commands\Server\Controllers\CPO\V2_2_1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Log;
use Ocpi\Models\Commands\Command;
use Ocpi\Models\Commands\Enums\CommandType;
use Ocpi\Models\PartyRole;
use Ocpi\Models\Sessions\Session;
use Ocpi\Modules\Commands\Events\CPO\CommandRemoteStartTransaction;
use Ocpi\Modules\Commands\Events\CPO\CommandRemoteStopTransaction;
use Ocpi\Modules\Commands\Factories\CommandTokenFactory;
use Ocpi\Modules\Credentials\Object\Party;
use Ocpi\Modules\Locations\Enums\TokenType;
use Ocpi\Support\Server\Controllers\Controller;

class PostController extends Controller
{
    public function __invoke(Request $request, string $commandType): JsonResponse
    {
        $commandType = CommandType::tryFrom($commandType);
        try {
            return match ($commandType) {
                CommandType::START_SESSION => $this->remoteStartTransaction($request),
                CommandType::STOP_SESSION => $this->remoteStopTransaction($request),
                CommandType::CANCEL_RESERVATION,
                CommandType::RESERVE_NOW,
                CommandType::UNLOCK_CONNECTOR => $this->ocpiServerErrorResponse(statusMessage: 'To be implemented')
            };
        } catch (Exception $e) {
            Log::channel('ocpi')->error($e->getMessage());
            return $this->ocpiServerErrorResponse();
        }
    }

    private function remoteStartTransaction(Request $request): JsonResponse
    {
        $token = CommandTokenFactory::fromArray($request->input('token'));
        if (TokenType::RFID === $token->getType()) {
            return $this->ocpiServerErrorResponse(statusMessage: 'RFID is not support yet.');
        }
        /** @var Party $party */
        $party = Context::get('party');
        try {
            /** @var \Ocpi\Modules\Credentials\Object\PartyRole $partyRole */
            $partyRole = $party->getRoles()->first();

            $payload = $request->toArray();
            $command = Command::query()->create([
                'party_role_id' => $partyRole->getId(),
                'type' => CommandType::START_SESSION,
                'payload' => $payload,
            ]);

            CommandRemoteStartTransaction::dispatch($command->id);
            return $this->ocpiCommandAcceptedResponse();
        } catch (Exception $e) {
            Log::channel('ocpi')->error($e->getMessage());
            return $this->ocpiServerErrorResponse();
        }
    }

    private function remoteStopTransaction(Request $request): JsonResponse
    {
        try {
            //@todo request class with validation the request for each commandType
            $session = Session::query()->findOrFail($request->get('session_id'));

            $payload = $request->toArray();
            $command = Command::query()->create([
                'party_role_id' => $session->party_role_id,
                'type' => CommandType::STOP_SESSION,
                'payload' => $payload,
            ]);

            CommandRemoteStopTransaction::dispatch($command->id, $request->input('session_id'));
            return $this->ocpiCommandAcceptedResponse();
        } catch (Exception $e) {
            Log::channel('ocpi')->error($e->getMessage());
            return $this->ocpiServerErrorResponse();
        }
    }
}
