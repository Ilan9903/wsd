<?php

namespace Hopla\FileOrFolderManagement\Providers;

use App\Models\FileOrFolder;
use Hopla\FileOrFolderManagement\Listeners\HandleFileCreated;
use Hopla\FileOrFolderManagement\Listeners\HandleFileForceDeleted;
use Hopla\FileOrFolderManagement\Listeners\HandleFileSaving;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        FileOrFolder::forceDeleted([
            HandleFileForceDeleted::class, 'handle',
        ]);
        FileOrFolder::created([
            HandleFileCreated::class, 'handle',
        ]);
        FileOrFolder::saving([
            HandleFileSaving::class, 'handle',
        ]);
    }
}
