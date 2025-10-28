<?php

namespace Ocpi\Modules\Versions\Server\Controllers\EMSP\V2_2_1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Route;
use Ocpi\Support\Enums\OcpiClientErrorCode;
use Ocpi\Support\Helpers\UrlHelper;
use Ocpi\Support\Server\Controllers\Controller;

use function Ocpi\Modules\Versions\Server\Controllers\EMSP\config;
use function Ocpi\Modules\Versions\Server\Controllers\EMSP\route;

class DetailsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if (!Context::has('ocpi_version')) {
            return $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::InvalidParameters,
                statusMessage: 'Unknown OCPI version.',
            );
        }

        $version = Context::get('ocpi_version');
        $data = null;

        foreach (
            config('ocpi-emsp.versions', []
            ) as $configVersion => $configInformation
        ) {
            if ($configVersion === $version) {
                $endpointList = collect(($configInformation['modules'] ?? []))
                    ->map(function ($module) use ($version) {
                        $route = UrlHelper::getEMSPBaseUrlByModule($module, $version);
                        $interfaceRole = $this->getEMSPInterfaceRoleByModule($module);
                        return Route::has($route)
                            ? [
                                'identifier' => $module,
                                'role' => $interfaceRole->value,
                                'url' => route($route),
                            ]
                            : null;
                    })
                    ->filter()
                    ->values()
                    ->toArray();

                if (count($endpointList) > 0) {
                    $data = [
                        'version' => $version,
                        'endpoints' => $endpointList,
                    ];
                }
            }
        }

        if ($data === null) {
            return $this->ocpiClientErrorResponse(
                statusCode: OcpiClientErrorCode::InvalidParameters,
                statusMessage: 'Unsupported OCPI version.',
            );
        }

        return $this->ocpiSuccessResponse($data);
    }
}
