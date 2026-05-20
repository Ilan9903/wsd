<?php

namespace Hopla\FileOrFolderManagement\Listeners;

use App\Models\FileOrFolder;
use Hopla\FileOrFolderManagement\Handlers\PathBuilder;
use Hopla\FileOrFolderManagement\Services\FileOrFolderService;

class HandleFileSaving
{
    /**
     * Handle the event.
     */
    public function handle(
        FileOrFolder $fileOrFolder,
    ): void {

        $alreadyExists = FileOrFolder::where('name', $fileOrFolder->name)
            ->where('parent_id', $fileOrFolder->parent_id)
            ->whereNot('id', $fileOrFolder->id)
            ->exists();

        if ($alreadyExists) {
            $fileOrFolder->name = FileOrFolderService::generateUniqueName($fileOrFolder, $fileOrFolder->parent);
        }

        if ($fileOrFolder->exists) {
            return;
        }

        if (! $fileOrFolder->user_id) {
            $fileOrFolder->user_id = $fileOrFolder->parent?->user->id ?? auth()->user()->id;
        }

        $fileOrFolder->path = PathBuilder::build($fileOrFolder->parent);
    }
}
