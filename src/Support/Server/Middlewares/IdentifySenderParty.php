<?php

namespace Ocpi\Support\Server\Middlewares;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Ocpi\Models\Party;
use Ocpi\Models\PartyToken;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Traits\Server\Response as ServerResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Identify a Sender party aka eMSP party
 */
class IdentifySenderParty
{
    use ServerResponse;

    public function handle(Request $request, Closure $next): Response
    {
        // Retrieve Authorization Token from header.
        $tokenB = $this->token($request, 'Token');
        if ($tokenB === null) {
            return $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::NotEnoughInformation,
                statusMessage: 'Authorization is missing.',
            );
        }
        $tokenB = Str::rtrim($tokenB);

        // Decode Token (OCPI version >= 2.2).
        $decodedTokenB = Party::decodeToken($tokenB);

        /** @var PartyToken $token */
        $token = PartyToken::query()->where('token', $tokenB)
            ->when(false !== $decodedTokenB, function (Builder $query) use ($decodedTokenB) {
                $query->orWhere('token', $decodedTokenB);
            })
            ->first();

        if (null === $token) {
            return $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::InvalidParameters,
                statusMessage: 'Invalid Authorization Token or Party.',
            );
        }

        // Add information to Context.
        Context::add('trace_id', Str::uuid()->toString());
        Context::add('party_code', $token->party->code);
        Context::add('token_id', $token->id);
        Context::addHidden('party', $token->party);

        return $next($request);
    }

    /**
     * @see \Illuminate\Http\Concerns\InteractsWithInput::bearerToken()
     */
    private function token(Request $request, string $prefix = 'Bearer'): ?string
    {
        $header = $request->header('Authorization', '');

        $prefix .= ' ';
        $position = strrpos($header, $prefix);

        if ($position !== false) {
            $header = substr($header, $position + strlen($prefix));

            return str_contains($header, ',') ? strstr($header, ',', true) : $header;
        }

        return null;
    }
}
