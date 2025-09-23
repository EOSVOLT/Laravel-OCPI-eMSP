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
use Ocpi\Support\Server\Controllers\Controller;

class PostController extends Controller
{
    public function __invoke(Request $request, CommandType $commandType): JsonResponse
    {
        try {
            //@todo request class with ocpi response
            $token = CommandTokenFactory::fromArray($request->input('token'));
            $partyRole = PartyRole::query()
                ->where('code', $token->getPartyCode())
                ->where('country_code', $token->getCountryCode())
                ->first();

            $payload = $request->validated();
            $commandType = Command::query()->create([
                'party_role_id' => $partyRole->id,
                'type' => $commandType,
                'payload' => $payload,
            ]);
            RemoteStartTransaction::dispatch($partyRole->id, $commandType->id, $commandType->type->name);

            return $this->ocpiSuccessResponse();
        } catch (Exception $e) {
            Log::channel('ocpi')->error($e->getMessage());
            return $this->ocpiServerErrorResponse();
        }
    }
}
