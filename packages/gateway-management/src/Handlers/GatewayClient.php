<?php

namespace Hopla\GatewayManagement\Handlers;

use App\Models\Client as ClientModel;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GatewayClient
{
    /**
     * @param  $tenantId
     * @return mixed
     *
     * @throws ConnectionException
     */
    public function checkClientWeDrop($tenantId): mixed
    {
        $response = Http::post(
            (app()->environment('local') ? 'http://' : 'https://')
            .config('gateway-management.wedrop_api_url').'/api/checktenant', [
                'tenant_name' => $tenantId,
            ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param  array  $url
     * @return array|mixed
     *
     * @throws ConnectionException
     */
    public function syncWedrop(array $url)
    {
        $wedropURL = $url['client_wdp'];

        $response = Http::post((
            app()->environment('local') ? 'http://' : 'https://')
            ."{$wedropURL}/api/syncwesend", [
                'wesend_url' => $url['client_wsd'],
            ]);

        if ($response->notFound()) {

            return [];
        }

        $client = ClientModel::where('domain', $url['client_wsd'])->first();

        $client->update([
            'wedrop_url' => $wedropURL,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param  $tenantId
     * @return array<string>
     *
     * @throws ConnectionException
     * @throws GuzzleException
     */
    public function getClientWeDrop($tenantId): array
    {
        $jsonResponse = $this->checkClientWeDrop($tenantId);

        \Log::alert($jsonResponse);

        if (! isset($jsonResponse['bucket']) || ! $jsonResponse['bucket']) {
            return [];
        }

        $client = ClientModel::where(
            'bucket', $jsonResponse['bucket']
        )->first();

        return [
            'message' => __('gateway.sync-wdp-success'),
            'client_wsd' => $client->domain,
            'client_wdp' => $jsonResponse['client'],
            'global_id_wsd_wdp' => $client->global_id,
        ];
    }
}
