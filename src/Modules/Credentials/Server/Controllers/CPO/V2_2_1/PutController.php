<?php

namespace Ocpi\Modules\Credentials\Server\Controllers\CPO\V2_2_1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Models\PartyToken;
use Ocpi\Modules\Credentials\Actions\Party\SelfCredentialsGetAction;
use Ocpi\Modules\Credentials\Events;
use Ocpi\Modules\Credentials\Object\PartyCode;
use Ocpi\Modules\Credentials\Validators\V2_2_1\CredentialsValidator;
use Ocpi\Modules\Versions\Actions\PartyInformationAndDetailsSynchronizeAction;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Enums\OcpiServerErrorCode;
use Ocpi\Support\Enums\Role;
use Ocpi\Support\Helpers\GeneratorHelper;
use Ocpi\Support\Server\Controllers\Controller;

class PutController extends Controller
{
    public function __invoke(
        Request $request,
        PartyInformationAndDetailsSynchronizeAction $versionsPartyInformationAndDetailsSynchronizeAction,
        SelfCredentialsGetAction $selfCredentialsGetAction,
    ): JsonResponse {
        try {
            $input = CredentialsValidator::validate($request->all());
            /** @var PartyToken $partyToken */
            $partyToken = PartyToken::query()->find(Context::get('token_id'));
            $parentParty = $partyToken->party;
            if ($parentParty === null) {
                return $this->ocpiServerErrorResponse(
                    statusCode: OcpiServerErrorCode::PartyApiUnusable,
                    statusMessage: 'Client not found.',
                    httpCode: 405,
                );
            }

            if (false === $parentParty->registered) {
                return $this->ocpiServerErrorResponse(
                    statusCode: OcpiServerErrorCode::PartyApiUnusable,
                    statusMessage: 'Client not registered.',
                    httpCode: 405,
                );
            }

            $parentParty = DB::connection(config('ocpi.database.connection'))
                ->transaction(
                    function () use (
                        $parentParty,
                        $request,
                        $input,
                        $versionsPartyInformationAndDetailsSynchronizeAction,
                        $partyToken
                    ) {
                        //remove all current children roles and recreate from payload.
                        $parentParty->children->each(function (Party $child) {
                            $child->roles()->delete();
                        });
                        $newTokenB = $input['token'];
                        $newUrl = $input['url'];
                        // update children parties from payload
                        foreach ($request->input('roles') as $role) {
                            $partyCode = new PartyCode($role['party_id'], $role['country_code']);

                            /** @var Party $childrenParty */
                            $childrenParty = $parentParty->children()->where(
                                'code',
                                $partyCode->getCodeFormatted()
                            )->first();
                            $childrenPartyToken = new PartyToken();
                            $tokenName = $role['business_details']['name'] ?? '';
                            $childrenPartyToken->fill([
                                'token' => $newTokenB,
                                'registered' => true,
                                'name' => $tokenName . "_" . $partyCode->getCodeFormatted(),
                            ]);
                            if (null === $childrenParty) {
                                $childrenParty = Party::query()->create(
                                    [
                                        'code' => $partyCode->getCodeFormatted(),
                                        'parent_id' => $parentParty->id,
                                        'url' => $newUrl,
                                        'version' => $parentParty->version,
                                    ]
                                );
                                $childrenParty->tokens()->save($childrenPartyToken);
                            } else {
                                $childrenParty->update([
                                    'url' => $newUrl,
                                ]);
                                $childrenParty->tokens()->delete();
                                $childrenParty->tokens()->save($childrenPartyToken);
                            }
                            // OCPI GET calls for Versions Information and Details of the Party, store OCPI endpoints.
                            $childrenParty = $versionsPartyInformationAndDetailsSynchronizeAction->handle(
                                $childrenParty,
                                $childrenPartyToken,
                                Role::tryFrom($role['role'])
                            );

                            $partyRole = new PartyRole;
                            $partyRole->fill([
                                'code' => $partyCode->getCode(),
                                'role' => $role['role'],
                                'country_code' => $partyCode->getCountryCode(),
                                'business_details' => $role['business_details'],
                            ]);
                            $childrenParty->roles()->save($partyRole);
                        }
                        // regenerate a new Token C for the client Party.
                        $partyToken->token = GeneratorHelper::generateToken($parentParty->code);
                        $partyToken->save();
                        $partyToken->refresh();
                        $parentParty->refresh();
                        return $parentParty;
                    }
                );

            Events\CredentialsUpdated::dispatch($parentParty->id, $request->json()->all());

            return $this->ocpiSuccessResponse(
                $selfCredentialsGetAction->handle($parentParty, $partyToken)
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
