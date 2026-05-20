<?php

namespace App\Services\Minio;

use App\Services\Kes\KesKey;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Hopla\KesManagement\Facades\KESManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/** @codeCoverageIgnore */
readonly class Minio
{
    public function __construct(
        private ?KesKey $kesKey,
    ) {}

    /**
     * @return S3Client
     */
    private function getS3Client(): S3Client
    {
        return new S3Client([
            'version' => 'latest',
            'region' => config('minio.minio_region'),
            'endpoint' => config('minio.minio_endpoint'),
            'credentials' => new Credentials(
                config('minio.minio_access_key'),
                config('minio.minio_access_key_secret')
            ),
            'use_path_style_endpoint' => true,
            'http' => [
                'verify' => config('minio.ssl_verify'),
            ],
        ]);
    }

    /**
     * @param  string  $bucketName
     * @return JsonResponse
     */
    public function createBucket(string $bucketName): JsonResponse
    {
        try {
            $s3Client = $this->getS3Client();

            if (! app()->environment(['local', 'RD']) && $this->kesKey instanceof KesKey) {
                KESManager::generateKesKey($bucketName);
            }

            $s3Client->createBucket([
                'Bucket' => $bucketName,
                'CreateBucketConfiguration' => ['LocationConstraint' => config('minio.minio_endpoint')],
            ]);

            if (! app()->environment(['local', 'RD']) && $this->kesKey instanceof KesKey) {
                $s3Client->putBucketEncryption([
                    'Bucket' => $bucketName,
                    'ServerSideEncryptionConfiguration' => [
                        'Rules' => [[
                            'ApplyServerSideEncryptionByDefault' => [
                                'SSEAlgorithm' => 'aws:kms',
                                'KMSMasterKeyID' => $bucketName,
                            ],
                        ]],
                    ],
                ]);
            }

            return response()->json([
                'message' => __('minio.created-encrypt-success', [
                    'kms_key_name' => $bucketName,
                ]),
            ]);

        } catch (AwsException $e) {
            Log::error(__('minio.s3-error', ['error' => $e->getMessage()]));

            return response()->json([
                'message' => __('minio.s3-error', ['error' => $e->getMessage()]),
            ], 500);
        }
    }

    /**
     * @param  string  $bucketName
     * @return JsonResponse
     */
    public function removeBucket(string $bucketName): JsonResponse
    {
        try {
            $s3Client = $this->getS3Client();

            do {
                $bucket = $s3Client->listObjectsV2([
                    'Bucket' => $bucketName,
                    'ContinuationToken' => $continuationToken ?? null,
                ]);

                if (! empty($bucket['Contents'])) {
                    $deleteBuckets = array_map(
                        fn ($deleteBucket) => ['Key' => $deleteBucket['Key']],
                        $bucket['Contents']
                    );

                    $s3Client->deleteObjects([
                        'Bucket' => $bucketName,
                        'Delete' => ['Objects' => $deleteBuckets],
                    ]);
                }

                $isTruncated = $bucket['IsTruncated'] ?? false;
                $continuationToken = $bucket['NextContinuationToken'] ?? null;

            } while ($isTruncated);

            $s3Client->deleteBucket(['Bucket' => $bucketName]);

            if (! app()->environment(['local', 'RD'])) {
                KESManager::removeKesKey($bucketName);
            }

            return response()->json([
                'message' => __('minio.bucket-kes-deleted-success'),
            ]);

        } catch (AwsException $e) {
            Log::error("Erreur AWS lors de la suppression du bucket {$bucketName} : {$e->getMessage()}");

            return response()->json([
                'message' => __('minio.s3-error', ['error' => $e->getMessage()]),
            ], 500);
        } catch (\Exception $e) {
            Log::error("Erreur inattendue lors de removeBucket : {$e->getMessage()}");

            return response()->json([
                'message' => __('minio.unknown-error', ['error' => $e->getMessage()]),
            ], 500);
        }
    }

    /**
     * @return array<int, string>
     */
    public function listBuckets(): array
    {
        try {
            $s3Client = $this->getS3Client();

            /** @var array{Buckets?: array<int, array{Name: string}>} $listBucket */
            $listBucket = $s3Client->listBuckets();

            $buckets = $listBucket['Buckets'] ?? [];

            return collect($buckets)
                ->pluck('Name')
                ->toArray();

        } catch (AwsException $e) {
            Log::error("Erreur AWS lors du listing des buckets : {$e->getMessage()}");

            return [];
        } catch (\Exception $e) {
            Log::error("Erreur inattendue lors du listing des buckets : {$e->getMessage()}");

            return [];
        }
    }
}
