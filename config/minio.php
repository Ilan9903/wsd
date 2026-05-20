<?php

return [

    'ssl_verify' => env('APP_SSL_VERIFY'),
    'minio_region' => env('MINIO_REGION'),
    'ssl_key' => env('SSL_KEY'),
    'ssl_cert' => env('SSL_CERT'),
    'kes_server' => env('MINIO_KMS_KES_SERVER'),
    'minio_access_key' => env('MINIO_ACCESS_KEY'),
    'minio_access_key_secret' => env('MINIO_SECRET_KEY'),
    'minio_endpoint' => env('MINIO_ENDPOINT'),
];
