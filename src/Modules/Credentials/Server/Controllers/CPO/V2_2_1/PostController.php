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
use Ocpi\Modules\Credentials\Actions\Party\CPO\SelfCredentialsGetAction;
use Ocpi\Modules\Credentials\Events;
use Ocpi\Modules\Credentials\Validators\V2_1_1\CredentialsValidator;
use Ocpi\Modules\Versions\Actions\CPO\PartyInformationAndDetailsSynchronizeAction as CPOSynchronizeDetailsAction;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Enums\OcpiServerErrorCode;
use Ocpi\Support\Server\Controllers\Controller;

use function Ocpi\Modules\Credentials\Server\Controllers\V2_1_1\config;

class PostController extends Controller
{
    public function __invoke(
        Request $request,
        CPOSynchronizeDetailsAction $synchronizeCPODetailsAction,
        SelfCredentialsGetAction $selfCredentialsGetAction,
    ): JsonResponse {
        try {
            $input = CredentialsValidator::validate($request->all());

            $partyCode = Context::get('party_code');
            /** @var Party $party */
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
            $party = DB::connection(config('ocpi.database.connection'))
                ->transaction(
                    function () use ($party, $request, $input, $synchronizeCPODetailsAction) {
                        // Update Server Token, url for the Party and mark it as registered.
                        $decodedToken = Party::decodeToken($input['token'], $party);
                        $party->client_token = false === $decodedToken ? $input['token'] : $decodedToken;
                        $party->url = $request->input('url');
                        $party->registered = true;

                        // OCPI GET calls for Versions Information and Details of the Party, store OCPI endpoints.
                        $party = $synchronizeCPODetailsAction->handle($party);

                        if ($party->roles->count() > 0) {
                            $party->roles()->delete();
                        }
                        foreach ($request->input('roles') as $role) {
                            // Update PartyRole list.
                            $partyRole = $party->roles
                                ->where('code', $role['party_id'])
                                ->where('country_code', $role['country_code'])
                                ->first();

                            if ($partyRole === null) {
                                $partyRole = new PartyRole;
                                $partyRole->fill([
                                    'code' => $role['party_id'],
                                    'role' => $role['role'],
                                    'country_code' => $role['country_code'],
                                    'business_details' => $role['business_details'],
                                ]);

                                $party->roles()->save($partyRole);
                            } else {
                                $partyRole->fill([
                                    'role' => $role['role'],
                                    'business_details' => $role['business_details'],
                                ]);
                                $partyRole->save();
                                $party->touch();
                            }
                        }
                        // Generate a Token C for the eMSP Party.
                        $party->server_token = $party->generateToken();
                        $party->save();
                        return $party;
                    }
                );

            Events\CredentialsCreated::dispatch($party->id, $request->json()->all());

            return $this->ocpiCreatedResponse(
                $selfCredentialsGetAction->handle($party)
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
