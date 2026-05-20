<?php

namespace Hopla\GatewayManagement\Http;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Link;
use Hopla\GatewayManagement\Facade\GatewayManager;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Octane\Exceptions\DdException;
use Spatie\WebhookServer\WebhookCall;
use Stancl\Tenancy\Facades\Tenancy;

class GatewayController extends Controller
{
    /**
     * @param  Request  $request
     * @return JsonResponse
     *
     * @throws DdException
     * @throws ConnectionException
     */
    public function sendDataFileFromLink(Request $request): JsonResponse
    {
        $linkUrl = $request->link_url;

        $client = Client::where('tenant', tenant()->id)->first();

        $userWDP = GatewayManager::getUserWeDrop();

        Tenancy::initialize($client->tenant);

        $link = Link::where('url', $linkUrl)->firstOrFail();

        $link
            ->fileOrFolder()
            ->select([
                'id',
                'parent_id',
                'mime_type',
                'name',
                'type',
                'size',
                'path',
                'created_at',
                'updated_at',
            ])
            ->chunk(100, function ($files) use ($client, $userWDP) {
                $files->each(function ($file) use ($client, $userWDP) {
                    WebhookCall::create()
                        ->url($client->wedrop_url.'/api/receive-files')
                        ->onQueue('default')
                        ->payload([
                            'meta_data' => $file,
                            'tenant' => $client->tenant,
                            'bucket' => $client->bucket,
                            'user_email' => $userWDP['user_email'],
                            'wesend_url' => (app()->environment('local') ? 'http://' : 'https://').$client->domain,
                        ])
                        ->useSecret(config('gateway-management.webhook_secret'))
                        ->dispatch();

                    \Log::alert('webhook send '.$file['name'].' to :'.$client->wedrop_url.'/api/receive-files');
                });
            });

        return response()->json([
            'message' => __('gateway.files-sent'),
        ]);
    }

    /**
     * @return JsonResponse
     *
     * @throws ConnectionException
     */
    public function getUserWeDrop(): JsonResponse
    {
        $getUserWDP = GatewayManager::getUserWeDrop();

        if ($getUserWDP->getStatusCode() === 404) {
            return response()->json(['error' => __('gateway.user-not-found-wdp')], 404);
        }

        return response()->json([
            'message' => __('gateway.user-found-wdp'),
            'tenant_wdp' => $getUserWDP['tenant'],
            'user_email' => $getUserWDP['user_email'],
        ]);
    }
}
