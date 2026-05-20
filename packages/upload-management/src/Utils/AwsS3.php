<?php

namespace Hopla\UploadManagement\Utils;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;

class AwsS3
{
    public function getS3Client(): S3Client
    {
        return new S3Client([
            'version' => 'latest',
            'region' => config('filesystems.disks.minio.region'),
            'endpoint' => config('filesystems.disks.minio.endpoint'),
            'credentials' => new Credentials(config('minio.minio_access_key'), config('minio.minio_access_key_secret')),
            'use_path_style_endpoint' => true,
        ]);
    }
}
