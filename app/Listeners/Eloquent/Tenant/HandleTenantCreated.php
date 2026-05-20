<?php

namespace App\Listeners\Eloquent\Tenant;

use App\Models\Tenant;
use App\Services\Minio\Minio;
use GuzzleHttp\Exception\GuzzleException;

readonly class HandleTenantCreated
{
    public function __construct(private Tenant $tenant) {}

    /**
     * @param  Minio  $minio
     * @return void
     *
     * @throws GuzzleException
     */
    public function handle(Minio $minio): void
    {

        $minio->createBucket((string) $this->tenant->id);
    }
}
