<?php

namespace Ocpi\Support\Server\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Ocpi\Models\PartyRole;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Traits\Server\Response as ServerResponse;
use Symfony\Component\HttpFoundation\Response;

class IdentifyPartyRole
{
    use ServerResponse;

    public function handle(Request $request, Closure $next): Response
    {
        // Retrieve PartyRole information from route.
        $partyRoleCountryCode = $request->route('country_code');
        $partyRoleCode = $request->route('party_id');

        if ($partyRoleCountryCode === null || $partyRoleCode === null) {
            return $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::NotEnoughInformation,
                statusMessage: 'Country code or Party ID is missing.',
            );
        }

        // Retrieve PartyRole.
        $partyRole = PartyRole::code($partyRoleCode)
            ->countryCode($partyRoleCountryCode)
            ->first();

        if ($partyRole === null) {
            return $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::InvalidParameters,
                statusMessage: 'Invalid Party.',
            );
        }

        // Add information to Context.
        Context::add('party_role_id', $partyRole->id);

        return $next($request);
    }
}
