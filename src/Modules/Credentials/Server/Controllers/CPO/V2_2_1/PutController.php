<?php

namespace Ocpi\Modules\Credentials\Server\Controllers\CPO\V2_2_1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Ocpi\Models\PartyRole;
use Ocpi\Models\PartyToken;
use Ocpi\Modules\Credentials\Actions\Party\SelfCredentialsGetAction;
use Ocpi\Modules\Credentials\Actions\PartyRole\SyncPartyRoleAction;
use Ocpi\Modules\Credentials\Validators\V2_2_1\CredentialsValidator;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Enums\OcpiServerErrorCode;
use Ocpi\Support\Helpers\GeneratorHelper;
use Ocpi\Support\Server\Controllers\Controller;

class PutController extends Controller
{
    public function __invoke(
        Request $request,
        SelfCredentialsGetAction $selfCredentialsGetAction,
        SyncPartyRoleAction $syncPartyRoleAction,
    ): JsonResponse {
        try {
            $input = CredentialsValidator::validate($request->all());
            /** @var PartyToken $parentToken */
            $parentToken = PartyToken::query()->find(Context::get('token_id'));
            $parentParty = $parentToken->party_role->party;
            if ($parentParty === null) {
                return $this->ocpiServerErrorResponse(
                    statusCode: OcpiServerErrorCode::PartyApiUnusable,
                    statusMessage: 'Client not found.',
                    httpCode: 405,
                );
            }

            if (false === $parentToken->registered) {
                return $this->ocpiServerErrorResponse(
                    statusCode: OcpiServerErrorCode::PartyApiUnusable,
                    statusMessage: 'Client not registered.',
                    httpCode: 405,
                );
            }

            $parentToken = DB::connection(config('ocpi.database.connection'))
                ->transaction(
                    function () use (
                        $parentParty,
                        $input,
                        $parentToken,
                        $syncPartyRoleAction
                    ) {
                        //remove all current children roles where it not exist in payload.
                        $existRoles = [];
                        //delete if not in the payload.
                        foreach ($input['roles'] as $role) {
                            /** @var PartyRole $exist */
                            $exist = $parentParty->role_cpo->children_roles
                                ->where('code', $role['party_id'])
                                ->where('role', $role['role'])
                                ->where('country_code', $role['country_code'])
                                ->first();
                            if (null !== $exist) {
                                $existRoles[] = $exist->id;
                            }
                        }
                        $parentParty->role_cpo->children_roles()->whereNotIn('id', $existRoles)->delete();

                        // Create client parties from payload
                        $syncPartyRoleAction->handle($parentToken, $input);
                        // Generate a Token C for the client Party.
                        $parentToken->token = GeneratorHelper::generateToken($parentParty->code);
                        $parentToken->registered = true;
                        $parentToken->save();
                        $parentToken->refresh();
                        return $parentToken;
                    }
                );

            return $this->ocpiSuccessResponse(
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
