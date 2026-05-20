<?php

namespace Hopla\FileOrFolderManagement\Listeners;

use App\Enums\FileOrFolderType;
use App\Models\FileOrFolder;
use Hopla\FileOrFolderManagement\Handlers\FileStorageManager;

readonly class HandleFileCreated
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private FileStorageManager $fileStorageManager
    ) {}

    /**
     * Handle the event.
     */
    public function handle(
        FileOrFolder $fileOrFolder,
    ): void {

        if ($fileOrFolder->type == FileOrFolderType::File) {
            $this->fileStorageManager->addToStorage($fileOrFolder);
        }
    }
}
