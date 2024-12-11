<?php

namespace Ocpi\Modules\Credentials\Server\Controllers\V2_1_1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Modules\Credentials\Actions\Party\SelfCredentialsGetAction;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Enums\OcpiServerErrorCode;
use Ocpi\Support\Server\Controllers\Controller;

class PostController extends Controller
{
    public function __invoke(Request $request, SelfCredentialsGetAction $selfCredentialsGetAction): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'url' => 'required',
            'party_id' => 'required',
            'country_code' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::InvalidParameters,
            );
        }

        $partyCode = Context::get('party_code');

        $party = Party::with(['roles'])->where('code', $partyCode)->first();
        if ($party === null) {
            return $this->ocpiServerErrorResponse(
                statusCode: OcpiServerErrorCode::PartyApiUnusable,
                statusMessage: 'Client not found.',
                httpCode: 405,
            );
        }

        if ($party->registered === true) {
            return $this->ocpiServerErrorResponse(
                statusCode: OcpiServerErrorCode::PartyApiUnusable,
                statusMessage: 'Client already registered.',
                httpCode: 405,
            );
        }

        $party->url = $request->input('url');
        $party->client_token = Party::decodeToken($request->input('token'), $party);
        $party->registered = true;

        try {
            // TODO: Fetch Client's endpoints for the version.

            DB::beginTransaction();

            $party->server_token = $party->generateToken();
            $party->save();

            $partyRole = $party->roles
                ->where('code', $request->input('party_id'))
                ->where('country_code', $request->input('country_code'))
                ->first();

            if ($partyRole === null) {
                if ($party->roles->count() > 0) {
                    $party->roles()->delete();
                }

                $partyRole = new PartyRole;
                $partyRole->fill([
                    'code' => $request->input('party_id'),
                    'role' => 'CPO',
                    'country_code' => $request->input('country_code'),
                    'business_details' => $request->input('business_details'),
                ]);

                $party->roles()->save($partyRole);
            } else {
                $partyRole->fill([
                    'role' => 'CPO',
                    'business_details' => $request->input('business_details'),
                ]);

                $partyRole->touch();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            Log::channel('ocpi')->error($e->getMessage());

            return $this->ocpiServerErrorResponse(
                statusCode: OcpiServerErrorCode::PartyApiUnusable
            );
        }

        return $this->ocpiCreatedResponse(
            $selfCredentialsGetAction->handle($party)
        );
    }
}
