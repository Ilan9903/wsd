<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckTenantRequest;
use App\Models\Client;
use App\Models\Theme;
use GuzzleHttp\Exception\GuzzleException;
use Hopla\GatewayManagement\Facade\GatewayManager;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Stancl\Tenancy\Facades\Tenancy;

class ClientController extends Controller
{
    /**
     * @param  CheckTenantRequest  $request
     * @return JsonResponse|RedirectResponse
     *
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function checkTenant(CheckTenantRequest $request): JsonResponse|RedirectResponse
    {
        $tenantId = $request->tenant_name;
        $client = Client::whereBucket($tenantId)->first();
        $domain = Domain::where('tenant_id', $tenantId)->first();
        if (! $domain) {
            return response()->json(['error' => __('client.not-found')], 404);
        }

        Tenancy::initialize($tenantId);

        $theme = Theme::where('status', '=', true)->first();

        return response()->json([
            'client' => $domain->domain,
            'global_id' => $client->global_id,
            'bucket' => $client->bucket,
            'theme' => $theme,
        ]);
    }

    /**
     * @param  CheckTenantRequest  $request
     * @return array<int, string>|JsonResponse
     *
     * @throws GuzzleException
     * @throws ConnectionException
     */
    public function checkGlobalClient(CheckTenantRequest $request): array|JsonResponse
    {
        $url = GatewayManager::getClientWeDrop($request->tenant_name);

        return GatewayManager::syncClientWeDrop($url[0]);
    }
}
