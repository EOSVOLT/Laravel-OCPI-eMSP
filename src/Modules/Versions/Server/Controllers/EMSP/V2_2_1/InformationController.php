<?php

namespace Ocpi\Modules\Versions\Server\Controllers\EMSP\V2_2_1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Ocpi\Support\Server\Controllers\Controller;

use function Ocpi\Modules\Versions\Server\Controllers\EMSP\config;
use function Ocpi\Modules\Versions\Server\Controllers\EMSP\route;

class InformationController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = collect(config('ocpi-emsp.versions', []))
            ->map(function ($moduleList, $version) {
                $route = config('ocpi.server.routing.emsp.name_prefix') . Str::replace(
                        '.',
                        '_',
                        $version
                    ) . '.versions.details';

                return Route::has($route)
                    ? [
                        'version' => $version,
                        'url' => route($route),
                    ]
                    : null;
            })
            ->filter()
            ->values()
            ->toArray();

        return $this->ocpiSuccessResponse($data);
    }
}
