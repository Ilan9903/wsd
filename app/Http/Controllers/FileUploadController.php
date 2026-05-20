<?php

namespace App\Http\Controllers;

use App\Services\Minio\Bucket;
use Hopla\FileOrFolderManagement\Handlers\FolderHierarchy;
use Hopla\UploadManagement\Facades\UploadManager;
use Hopla\UploadManagement\Http\Requests\AbortMultipartUploadRequest;
use Hopla\UploadManagement\Http\Requests\CompleteMultipartUploadRequest;
use Hopla\UploadManagement\Http\Requests\CreateMultipartUploadRequest;
use Hopla\UploadManagement\Http\Requests\SignPartRequest;
use Hopla\UploadManagement\Utils\AwsS3;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use JsonException;

class FileUploadController extends Controller
{
    /**
     * @param  CreateMultipartUploadRequest  $createRequest
     * @param  AwsS3  $awsS3
     * @param  Bucket  $bucketMinio
     * @return JsonResponse
     *
     * @throws JsonException
     */
    public function create(CreateMultipartUploadRequest $createRequest, AwsS3 $awsS3, Bucket $bucketMinio): JsonResponse
    {
        $keyUpload = trim('/upload/'.Str::uuid().'/'.$createRequest->filename, '/');

        $resultUpload = $awsS3->getS3Client()->createMultipartUpload([
            'Bucket' => $bucketMinio->getBucket(),
            'Key' => $keyUpload,
            'ContentType' => $createRequest->type ?? 'application/octet-stream',
        ]);

        $uploadFile = UploadManager::getUploadedFileCreate($resultUpload);

        $uploadFile->createOnDisk($createRequest->size, [
            'name' => $createRequest->filename,
            'type' => $createRequest->type ?? 'application/octet-stream',
            'size' => $createRequest->filesize,
            'tempPath' => $keyUpload,
            'parentId' => $createRequest->parent_id,
            'relativePath' => $createRequest->path,
        ]);

        return response()->json([
            'uploadId' => $resultUpload['UploadId'],
            'key' => $keyUpload,
        ]);
    }

    /**
     * @param  SignPartRequest  $signPartRequest
     * @param  AwsS3  $awsS3
     * @param  Bucket  $bucketMinio
     * @return JsonResponse
     */
    public function signPart(SignPartRequest $signPartRequest, AwsS3 $awsS3, Bucket $bucketMinio)
    {
        $cmd = $awsS3->getS3Client()->getCommand('UploadPart', [
            'Bucket' => $bucketMinio->getBucket(),
            'Key' => $signPartRequest->key,
            'UploadId' => $signPartRequest->uploadId,
            'PartNumber' => $signPartRequest->partNumber,
        ]);
        $presignedRequest = $awsS3->getS3Client()->createPresignedRequest($cmd, '+1 hour');

        return response()->json([
            'url' => (string) $presignedRequest->getUri(),
        ]);
    }

    /**
     * @param  CompleteMultipartUploadRequest  $completeRequest
     * @param  AwsS3  $awsS3
     * @param  Bucket  $bucketMinio
     * @param  FolderHierarchy  $folderHierarchy
     * @return JsonResponse
     *
     * @throws LockTimeoutException
     */
    public function complete(CompleteMultipartUploadRequest $completeRequest, AwsS3 $awsS3, Bucket $bucketMinio, FolderHierarchy $folderHierarchy)
    {
        $parts = UploadManager::getParts($completeRequest);

        $awsS3->getS3Client()->completeMultipartUpload([
            'Bucket' => $bucketMinio->getBucket(),
            'Key' => $completeRequest->key,
            'UploadId' => $completeRequest->uploadId,
            'MultipartUpload' => ['Parts' => $parts],
        ]);

        $uploadedFile = UploadManager::getUploadedFileComplete($completeRequest);

        $uploadBaseFolder = UploadManager::getUploadBaseFolder($uploadedFile);
        $realParentFolder = $folderHierarchy->createFolderHierarchy($uploadedFile, $uploadBaseFolder);

        $fileSytemItem = UploadManager::saveFileToS3($realParentFolder, $uploadedFile);

        return response()->json([
            'location' => $resultUpload['Location'] ?? null,
            'key' => $completeRequest->key,
            'fileSytemItem_id' => $fileSytemItem->id,
        ]);
    }

    /**
     * @param  AbortMultipartUploadRequest  $abortRequest
     * @param  AwsS3  $awsS3
     * @param  Bucket  $bucketMinio
     * @return JsonResponse
     */
    public function abort(AbortMultipartUploadRequest $abortRequest, AwsS3 $awsS3, Bucket $bucketMinio)
    {
        UploadManager::getUploadedFileCreate(
            $awsS3->getS3Client()->abortMultipartUpload([
                'Bucket' => $bucketMinio->getBucket(),
                'Key' => $abortRequest->key,
                'UploadId' => $abortRequest->uploadId,
            ]))->removeInfo();

        return response()->json(['aborted' => true]);
    }
}
