<?php

namespace Ocpi\Modules\Credentials\Server\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Modules\Credentials\Actions\Party\SelfCredentialsGetAction;
use Ocpi\Support\Enums\OcpiServerErrorCode;
use Ocpi\Support\Server\Controllers\Controller as BaseController;

class PutController extends BaseController
{
    public function __invoke(Request $request, SelfCredentialsGetAction $selfCredentialsGetAction): JsonResponse
    {
        $partyCode = Context::get('party_code');

        $party = Party::with(['roles'])->where('code', $partyCode)->first();
        if ($party === null) {
            return $this->ocpiServerErrorResponse(
                statusCode: OcpiServerErrorCode::PartyApiUnusable,
                statusMessage: 'Client not found.',
                httpCode: 405,
            );
        }

        if ($party->registered === false) {
            return $this->ocpiServerErrorResponse(
                statusCode: OcpiServerErrorCode::PartyApiUnusable,
                statusMessage: 'Client not registered.',
                httpCode: 405,
            );
        }

        $party->url = $request->input('url', $party->url);
        $party->client_token = $request->has('token') ? Party::decodeToken($request->input('token')) : $party->client_token;

        $partyRole = new PartyRole;
        $partyRole->fill([
            'code' => $request->input('party_id'),
            'role' => 'CPO',
            'country_code' => $request->input('country_code'),
            'business_details' => $request->input('business_details'),
        ]);

        try {
            // TODO: Fetch Client's endpoints if version is different from the current version.

            DB::beginTransaction();
            $party->server_token = $party->generateToken();

            $party->roles()->delete();
            $party->save();
            $party->roles()->save($partyRole);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            Log::channel('ocpi')->error($e->getMessage());

            return $this->ocpiServerErrorResponse(
                statusCode: OcpiServerErrorCode::PartyApiUnusable
            );
        }

        return $this->ocpiSuccessResponse(
            $selfCredentialsGetAction->handle($party)
        );
    }
}
