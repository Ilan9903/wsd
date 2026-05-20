<?php

namespace Hopla\GatewayManagement\Handlers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Promises\LazyPromise;
use Illuminate\Http\Client\Response;

class GatewayManager
{
    protected Client $client;

    public function __construct(
        protected GatewayClient $gatewayClient,
        protected GatewayUser $gatewayUser,
    ) {}

    /**
     * @param  $tenantId
     * @return array<string>
     *
     * @throws ConnectionException
     */
    public function checkClientWeDrop($tenantId): array
    {
        return $this->gatewayClient->checkClientWeDrop($tenantId);
    }

    /**
     * @param  $tenantId
     * @return array
     *
     * @throws ConnectionException|GuzzleException
     */
    public function getClientWeDrop($tenantId): array
    {
        return $this->gatewayClient->getClientWeDrop($tenantId);
    }

    /**
     * @param  $url
     * @return array|mixed
     *
     * @throws ConnectionException
     */
    public function syncClientWeDrop($url)
    {
        return $this->gatewayClient->syncWedrop($url);
    }

    /**
     * @return LazyPromise|PromiseInterface|Response|array<mixed>
     *
     * @throws ConnectionException
     */
    public function getUserWeDrop(): LazyPromise|PromiseInterface|Response|array
    {
        return $this->gatewayUser->getUserWeDrop();
    }
}
