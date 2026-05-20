<?php

namespace Hopla\GatewayManagement\Handlers;

use App\Models\Client;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Promises\LazyPromise;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class GatewayUser
{
    /**
     * @return LazyPromise|PromiseInterface|Response|array<mixed>
     *
     * @throws ConnectionException
     */
    public function getUserWeDrop(): LazyPromise|PromiseInterface|Response|array
    {
        $client = Client::where('tenant', tenant()->id)->firstOrFail();

        return Http::post(
            (app()->environment('local') ? 'http://' : 'https://')
            .config('gateway-management.wedrop_api_url')
            .'/api/sync-user-wesend', [
                'wesend_url' => (app()->environment('local') ? 'http://' : 'https://').$client->domain,
                'user_email' => auth()->user()->email,
            ]);
    }
}
