<?php

namespace Ocpi\Modules\Credentials\Actions\Party\CPO;

use Illuminate\Support\Facades\Route;
use Ocpi\Models\Party;

class SelfCredentialsGetAction
{
    public function handle(Party $party): ?array
    {
        $urlRoute = config('ocpi.server.routing.cpo.name_prefix').'versions.information';

        if (false === Route::has($urlRoute)) {
            return null;
        }
        if (version_compare($party->version, '2.2', '<')) {
            return [
                'url' => route($urlRoute),
                'token' => $party->encoded_server_token, //token C
                'party_id' => config('ocpi-cpo.party.party_id'),
                'country_code' => config('ocpi-cpo.party.country_code'),
                'business_details' => [
                    'name' => config('ocpi-cpo.party.business_details.name'),
                    'website' => config('ocpi-cpo.party.business_details.website'),
                ],
            ];
        }else{
            return [
                'url' => route($urlRoute),
                'token' => $party->encoded_server_token, //token C
                'roles'=> [
                    [
                        "role" => 'EMSP',
                        'party_id' => config('ocpi-cpo.party.party_id'),
                        'country_code' => config('ocpi-cpo.party.country_code'),
                        'business_details' => [
                            'name' => config('ocpi-cpo.party.business_details.name'),
                            'website' => config('ocpi-cpo.party.business_details.website'),
                        ],
                    ]
                ],
            ];
        }
    }
}
