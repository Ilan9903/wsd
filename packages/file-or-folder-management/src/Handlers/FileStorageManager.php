<?php

namespace Hopla\FileOrFolderManagement\Handlers;

use App\Models\FileOrFolder;
use App\Models\TenantContract;
use Stancl\Tenancy\Events\SyncedResourceSaved;

class FileStorageManager
{
    public function addToStorage(FileOrFolder $file): void
    {
        $user = $file->user;

        $fileSize = $file->size ?? 0;

        $user->increment('used_storage', $fileSize);

        $tenantContract = TenantContract::first();
        $tenantContract->increment('used_storage', $fileSize);

        event(new SyncedResourceSaved($tenantContract, tenant()));
    }

    public function removeToStorage(FileOrFolder $file): void
    {
        $user = $file->user;

        $fileSize = $file->size ?? 0;

        if ($user->used_storage > 0) {
            $user->decrement('used_storage', $fileSize);
        }

        $tenantContract = TenantContract::first();
        if ($tenantContract->used_storage > 0) {
            $tenantContract->decrement('used_storage', $fileSize);
        }

        event(new SyncedResourceSaved($tenantContract, tenant()));
    }
}
