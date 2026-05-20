<?php

namespace Hopla\DownloadManagement\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class FileNotFoundInMinioException extends Exception
{
    public function __construct(string $path = '')
    {
        $message = "Le fichier {$path} n'existe pas dans MinIO.";
        parent::__construct($message);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'error' => $this->getMessage(),
        ], 400);
    }
}
