<?php

namespace Ocpi\Modules\Credentials\Server\Controllers\EMSP\V2_2_1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Ocpi\Models\PartyToken;
use Ocpi\Modules\Credentials\Actions\Party\SelfCredentialsGetAction;
use Ocpi\Modules\Credentials\Actions\PartyRole\SyncPartyRoleAction;
use Ocpi\Modules\Credentials\Events;
use Ocpi\Modules\Credentials\Object\PartyCode;
use Ocpi\Modules\Credentials\Validators\V2_2_1\CredentialsValidator;
use Ocpi\Modules\Versions\Actions\PartyInformationAndDetailsSynchronizeAction;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Enums\OcpiServerErrorCode;
use Ocpi\Support\Enums\Role;
use Ocpi\Support\Helpers\GeneratorHelper;
use Ocpi\Support\Server\Controllers\Controller;

class PostController extends Controller
{
    public function __invoke(
        Request $request,
        SelfCredentialsGetAction $selfCredentialsGetAction,
        SyncPartyRoleAction $syncPartyRoleAction,
    ): JsonResponse {
        try {
            $input = \Ocpi\Modules\Credentials\Validators\V2_2_1\CredentialsValidator::validate($request->all());
            /** @var PartyToken $parentToken */
            $parentToken = PartyToken::query()->find(Context::get('token_id'));
            $parentParty = $parentToken->party_role->party;
            if (null === $parentParty) {
                return $this->ocpiServerErrorResponse(
                    statusCode: OcpiServerErrorCode::PartyApiUnusable,
                    statusMessage: 'Party not found.',
                    httpCode: 405,
                );
            }

            if (true === $parentToken->registered) {
                return $this->ocpiServerErrorResponse(
                    statusCode: OcpiServerErrorCode::PartyApiUnusable,
                    statusMessage: 'Party already registered.',
                    httpCode: 405,
                );
            }
            $parentToken = DB::connection(config('ocpi.database.connection'))
                ->transaction(
                    function () use (
                        $parentParty,
                        $request,
                        $input,
                        $parentToken,
                        $syncPartyRoleAction
                    ) {
                        // Create client parties from payload
                        $syncPartyRoleAction->handle($parentParty, $input);
                        // Generate a Token C for the client Party.
                        $parentToken->token = GeneratorHelper::generateToken($parentParty->code);
                        $parentToken->registered = true;
                        $parentToken->save();
                        $parentToken->refresh();
                        return $parentToken;
                    }
                );

            return $this->ocpiCreatedResponse(
                $selfCredentialsGetAction->handle($parentToken)
            );
        } catch (ValidationException $e) {
            Log::channel('ocpi')->error($e->getMessage());

            return $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::InvalidParameters,
                statusMessage: $e->getMessage(),
            );
        } catch (Exception $e) {
            Log::channel('ocpi')->error($e->getMessage());

            return $this->ocpiServerErrorResponse(
                statusCode: OcpiServerErrorCode::PartyApiUnusable,
            );
        }
    }
}
