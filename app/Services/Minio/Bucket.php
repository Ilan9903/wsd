<?php

namespace App\Services\Minio;

use App\Models\TenantContract;
use Illuminate\Support\Facades\Config;

/** @codeCoverageIgnore */
class Bucket
{
    /**
     * @return mixed|string
     */
    public function getBucket(): mixed
    {
        $bucket = TenantContract::first()->bucket;
        Config::set('filesystems.disks.minio.bucket', $bucket);

        return Config::get('filesystems.disks.minio.bucket');
    }
}
