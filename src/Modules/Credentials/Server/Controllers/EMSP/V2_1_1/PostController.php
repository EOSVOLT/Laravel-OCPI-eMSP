<?php

namespace Ocpi\Modules\Credentials\Server\Controllers\EMSP\V2_1_1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Ocpi\Models\Party;
use Ocpi\Models\PartyRole;
use Ocpi\Modules\Credentials\Actions\Party\SelfCredentialsGetAction;
use Ocpi\Modules\Credentials\Events;
use Ocpi\Modules\Credentials\Object\PartyCode;
use Ocpi\Modules\Credentials\Validators\V2_1_1\CredentialsValidator;
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
            /** @var Party $parentParty */
            $parentParty = Party::with(['roles'])->where('code', Context::get('party_code'))->first();
            if ($parentParty === null) {
                return $this->ocpiServerErrorResponse(
                    statusCode: OcpiServerErrorCode::PartyApiUnusable,
                    statusMessage: 'Client not found.',
                    httpCode: 405,
                );
            }

            if ($parentParty->registered === true) {
                return $this->ocpiServerErrorResponse(
                    statusCode: OcpiServerErrorCode::PartyApiUnusable,
                    statusMessage: 'Client already registered.',
                    httpCode: 405,
                );
            }

            $parentParty = DB::connection(config('ocpi.database.connection'))
                ->transaction(function () use ($parentParty, $request, $input, $versionsPartyInformationAndDetailsSynchronizeAction) {
                    $serverToken = GeneratorHelper::decodeToken($input['token'], $parentParty->version);
                    $url = $input['url'];
                    $partyCode = new PartyCode($request->input('party_id'), $request->input('country_code'));
                    $childrenParty = $parentParty->children()->where('code', $partyCode->getCodeFormatted())->first();
                    if (null === $childrenParty) {
                        $childrenParty = Party::query()->create(
                            [
                                'code' => $partyCode->getCodeFormatted(),
                                'parent_id' => $parentParty->id,
                                'name' => $parentParty->name . '_' . $partyCode->getCodeFormatted(),
                                'server_token' => $serverToken,
                                'url' => $url,
                                'version' => $parentParty->version,
                                'registered' => true,
                            ]
                        );
                        // OCPI GET calls for Versions Information and Details of the Party, store OCPI endpoints.
                        $childrenParty = $versionsPartyInformationAndDetailsSynchronizeAction->handle($childrenParty);
                        $partyRole = new PartyRole;
                        $partyRole->fill([
                            'code' => $partyCode->getCode(),
                            'role' => $request->input('role'),
                            'country_code' => $partyCode->getCountryCode(),
                            'business_details' => $request->input('business_details'),
                        ]);
                        $childrenParty->roles()->save($partyRole);
                    }

                    // Generate a Token C for the client party.
                    $parentParty->server_token = $parentParty->generateToken();
                    $parentParty->registered = true;
                    $parentParty->save();

                    return $parentParty;
                });

            Events\CredentialsCreated::dispatch($parentParty->id, $request->json()->all());

            return $this->ocpiCreatedResponse(
                $selfCredentialsGetAction->handle($parentParty)
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
