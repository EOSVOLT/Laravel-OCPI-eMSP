<?php

namespace Ocpi\Modules\Credentials\Server\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Ocpi\Models\Party;
use Ocpi\Models\PartyToken;
use Ocpi\Modules\Credentials\Actions\Party\SelfCredentialsGetAction;
use Ocpi\Support\Enums\OcpiServerErrorCode;
use Ocpi\Support\Server\Controllers\Controller;

class GetController extends Controller
{
    public function __invoke(Request $request, SelfCredentialsGetAction $selfCredentialsGetAction): JsonResponse
    {
        $parentToken = PartyToken::query()->with(['party_role.party'])->find(Context::get('token_id'));
        $party = $parentToken->party_role->party;
        if ($party === null) {
            return $this->ocpiServerErrorResponse(
                statusCode: OcpiServerErrorCode::PartyApiUnusable
            );
        }

        $data = $selfCredentialsGetAction->handle($parentToken);

        return $data
            ? $this->ocpiSuccessResponse($data)
            : $this->ocpiServerErrorResponse(
                statusCode: OcpiServerErrorCode::PartyApiUnusable
            );
    }
}
