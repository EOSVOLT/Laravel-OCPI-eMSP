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
use Ocpi\Support\Helpers\GeneratorHelper;
use Ocpi\Support\Server\Controllers\Controller;

class PostController extends Controller
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
            if (null === $parentParty) {
                return $this->ocpiServerErrorResponse(
                    statusCode: OcpiServerErrorCode::PartyApiUnusable,
                    statusMessage: 'Party not found.',
                    httpCode: 405,
                );
            }

            if (true === $partyToken->registered) {
                return $this->ocpiServerErrorResponse(
                    statusCode: OcpiServerErrorCode::PartyApiUnusable,
                    statusMessage: 'Party already registered.',
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
                        // Create client parties from payload
                        $tokenB = $input['token'];
                        $url = $input['url'];
                        foreach ($request->input('roles') as $role) {
                            $partyCode = new PartyCode($role['party_id'], $role['country_code']);

                            $childrenParty = $parentParty->children()->where(
                                'code',
                                $partyCode->getCodeFormatted()
                            )->first();
                            if ($childrenParty === null) {
                                $childrenParty = Party::query()->create(
                                    [
                                        'code' => $partyCode->getCodeFormatted(),
                                        'parent_id' => $parentParty->id,
                                        'url' => $url,
                                        'version' => $parentParty->version,
                                    ]
                                );
                                $childrenPartyToken = new PartyToken();
                                $tokenName = $role['business_details']['name'] ?? '';
                                $childrenPartyToken->fill([
                                    'token' => $tokenB,
                                    'registered' => true,
                                    'name' => $tokenName . '_' . $partyCode->getCodeFormatted(),
                                ]);
                                $childrenParty->tokens()->save($childrenPartyToken);
                                $partyRole = new PartyRole;
                                $partyRole->fill([
                                    'code' => $partyCode->getCode(),
                                    'role' => $role['role'],
                                    'country_code' => $partyCode->getCountryCode(),
                                    'business_details' => $role['business_details'],
                                ]);
                                $childrenParty->roles()->save($partyRole);
                                // OCPI GET calls for Versions Information and Details of the Party, store OCPI endpoints.
                                $versionsPartyInformationAndDetailsSynchronizeAction->handle(
                                    $partyRole,
                                    $childrenPartyToken,
                                );
                            }
                        }
                        // Generate a Token C for the client Party.
                        $partyToken->token = GeneratorHelper::generateToken($parentParty->code);
                        $partyToken->registered = true;
                        $partyToken->save();
                        $partyToken->refresh();
                        return $parentParty;
                    }
                );

            Events\CredentialsCreated::dispatch($parentParty->id, $request->json()->all());

            return $this->ocpiCreatedResponse(
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
