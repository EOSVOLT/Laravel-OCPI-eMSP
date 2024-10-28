<?php

namespace Ocpi\Support\Server\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Ocpi\Models\Party;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Traits\Server\Response as ServerResponse;
use Symfony\Component\HttpFoundation\Response;

class IdentifyParty
{
    use ServerResponse;

    public function handle(Request $request, Closure $next): Response
    {
        // Retrieve Authorization Token from header.
        $clientTokenEncoded = $this->token($request, 'Token');
        if ($clientTokenEncoded === null) {
            return $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::NotEnoughInformation,
                statusMessage: 'Authorization is missing.',
            );
        }

        // Decode Token.
        $clientToken = Party::decodeToken($clientTokenEncoded);
        if ($clientToken === false) {
            return $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::NotEnoughInformation,
                statusMessage: 'Invalid Authorization Token.',
            );
        }
        $clientToken = Str::rtrim($clientToken);

        // Retrieve Party from Token.
        $party = Party::where('client_token', $clientToken)->first();
        if ($party === null) {
            return $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::InvalidParameters,
                statusMessage: 'Invalid Party.',
            );
        }

        // Add information to Context.
        Context::add('trace_id', Str::uuid()->toString());
        Context::add('party_code', $party->code);
        Context::addHidden('party', $party);

        return $next($request);
    }

    /**
     * @see \Illuminate\Http\Concerns\InteractsWithInput::bearerToken()
     */
    private function token(Request $request, string $prefix = 'Bearer'): ?string
    {
        $header = $request->header('Authorization', '');

        $position = strrpos($header, $prefix.' ');

        if ($position !== false) {
            $header = substr($header, $position + strlen($prefix));

            return str_contains($header, ',') ? strstr($header, ',', true) : $header;
        }

        return null;
    }
}
