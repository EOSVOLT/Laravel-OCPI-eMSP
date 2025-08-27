<?php

namespace Ocpi\Modules\Credentials\Actions\Party\EMSP;

use Illuminate\Support\Facades\Route;
use Ocpi\Models\Party;

class SelfCredentialsGetAction
{
    public function handle(Party $party): ?array
    {
        $urlRoute = config('ocpi.server.routing.emsp.name_prefix').'versions.information';

        if (false === Route::has($urlRoute)) {
            return null;
        }
        if (version_compare($party->version, '2.2', '<')) {
            return [
                'url' => route($urlRoute),
                'token' => $party->encoded_client_token,
                'party_id' => config('ocpi-emsp.party.party_id'),
                'country_code' => config('ocpi-emsp.party.country_code'),
                'business_details' => [
                    'name' => config('ocpi-emsp.party.business_details.name'),
                    'website' => config('ocpi-emsp.party.business_details.website'),
                ],
            ];
        }else{
            return [
                'url' => route($urlRoute),
                'token' => $party->encoded_client_token,
                'roles'=> [
                    [
                        "role" => 'CPO',
                        'party_id' => config('ocpi-emsp.party.party_id'),
                        'country_code' => config('ocpi-emsp.party.country_code'),
                        'business_details' => [
                            'name' => config('ocpi-emsp.party.business_details.name'),
                            'website' => config('ocpi-emsp.party.business_details.website'),
                        ],
                    ]
                ],
            ];
        }
    }
}
