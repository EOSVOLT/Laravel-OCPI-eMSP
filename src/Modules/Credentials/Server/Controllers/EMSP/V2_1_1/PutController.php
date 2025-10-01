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

class PutController extends Controller
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

            if ($parentParty->registered === false) {
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
                        $versionsPartyInformationAndDetailsSynchronizeAction
                    ) {
                        //remove all current children roles and recreate from payload.
                        $parentParty->children->each(function (Party $child) {
                            $child->roles()->delete();
                        });

                        $newServerToken = $input['token'];
                        $newUrl = $input['url'];
                        // update children parties from payload
                        $partyCode = new PartyCode($request->input('party_id'), $request->input('country_code'));

                        /** @var Party $childrenParty */
                        $childrenParty = $parentParty->children()->where('code', $partyCode->getCodeFormatted())->first(
                        );
                        if ($childrenParty === null) {
                            $childrenParty = Party::query()->create(
                                [
                                    'code' => $partyCode->getCodeFormatted(),
                                    'parent_id' => $parentParty->id,
                                    'server_token' => $newServerToken,
                                    'url' => $newUrl,
                                    'version' => $parentParty->version,
                                    'registered' => true,
                                ]
                            );
                        } else {
                            $childrenParty->update([
                                'server_token' => $newServerToken,
                                'url' => $newUrl,
                            ]);
                        }
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
                        // regenerate a new Token C for the client Party.
                        $parentParty->server_token = $parentParty->generateToken();
                        $parentParty->save();
                        $parentParty->refresh();
                        return $parentParty;
                    }
                );

            Events\CredentialsUpdated::dispatch($parentParty->id, $request->json()->all());

            return $this->ocpiSuccessResponse(
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
