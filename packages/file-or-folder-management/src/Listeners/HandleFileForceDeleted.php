<?php

namespace Hopla\FileOrFolderManagement\Listeners;

use App\Enums\FileOrFolderType;
use App\Models\FileOrFolder;
use Hopla\FileOrFolderManagement\Handlers\FileStorageManager;

readonly class HandleFileForceDeleted
{
    public function __construct(
        private FileStorageManager $fileStorageManager
    ) {}

    public function handle(
        FileOrFolder $fileOrFolder,
    ): void {

        if ($fileOrFolder->type == FileOrFolderType::File) {
            $this->fileStorageManager->removeToStorage($fileOrFolder);
        }

    }
}
