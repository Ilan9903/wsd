<?php

namespace Hopla\FileOrFolderManagement\Handlers;

use App\Enums\FileOrFolderType;
use App\Models\FileOrFolder;
use Hopla\FileOrFolderManagement\DTO\UploadedFile;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class FolderHierarchy
{
    /**
     * @param  UploadedFile  $file
     * @param  FileOrFolder|null  $uploadBaseFolder
     * @return FileOrFolder|null
     *
     * @throws LockTimeoutException
     */
    public function createFolderHierarchy(
        UploadedFile $file,
        ?FileOrFolder $uploadBaseFolder
    ): ?FileOrFolder {
        $currentParentId = $uploadBaseFolder?->id;
        $parts = $this->extractPathParts($file);

        if (empty($parts)) {
            return $uploadBaseFolder;
        }

        foreach ($parts as $folderName) {
            $currentParentId = $this->createOrRetrieveFolder($folderName, $currentParentId);
        }

        return FileOrFolder::find($currentParentId);
    }

    /**
     * @param  UploadedFile  $file
     * @return array<string>
     */
    private function extractPathParts(UploadedFile $file): array
    {
        $metadata = $file->getInfo()['metadata'];
        $relativePath = $metadata['relativePath'] ?? '';

        return PathManager::getPathParts($relativePath);
    }

    /**
     * @param  string  $folderName
     * @param  string|null  $parentId
     * @return string
     *
     * @throws LockTimeoutException
     */
    private function createOrRetrieveFolder(string $folderName, ?string $parentId): string
    {
        $lockKey = 'create-folder:'.md5($folderName.'|'.($parentId ?? 'root'));
        $folderId = null;

        Cache::lock($lockKey, 5)->block(5, function () use (&$folderId, $folderName, $parentId) {
            $folderId = $this->findOrCreateFolder($folderName, $parentId);
        });

        return $folderId;
    }

    /**
     * @param  string  $folderName
     * @param  string|null  $parentId
     * @return string
     */
    private function findOrCreateFolder(string $folderName, ?string $parentId): string
    {
        $attributes = [
            'name' => $folderName,
            'type' => FileOrFolderType::Folder,
            'parent_id' => $parentId,
        ];

        $folder = FileOrFolder::where($attributes)->withTrashed()->first();

        if ($folder?->deleted_at) {
            abort(409, 'conflict');
        }

        if (! $folder) {
            $folder = FileOrFolder::create([
                ...$attributes,
                'user_id' => Auth::id(),
            ]);
        }

        return $folder->id;
    }
}
