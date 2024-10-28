<?php

namespace Ocpi\Modules\Credentials\Server\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ocpi\Models\Party;
use Ocpi\Support\Enums\OcpiServerErrorCode;
use Ocpi\Support\Server\Controllers\Controller as BaseController;

class DeleteController extends BaseController
{
    public function __invoke(Request $request): JsonResponse
    {
        $party = Party::where('code', Context::get('party_code'))->first();
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

        $party->registered = false;

        try {
            DB::beginTransaction();

            $party->save();
            $party->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            Log::channel('ocpi')->error($e->getMessage());

            return $this->ocpiServerErrorResponse(
                statusCode: OcpiServerErrorCode::PartyApiUnusable
            );
        }

        return $this->ocpiSuccessResponse();
    }
}
