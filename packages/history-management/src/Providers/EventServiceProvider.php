<?php

namespace Hopla\HistoryManagement\Providers;

use App\Models\Link;
use Hopla\HistoryManagement\Listeners\Link\HandleLinkCreated;
use Hopla\HistoryManagement\Listeners\Link\HandleLinkForceDeleted;
use Hopla\HistoryManagement\Listeners\Link\HandleLinkRestored;
use Hopla\HistoryManagement\Listeners\Link\HandleLinkSoftDeleted;
use Hopla\HistoryManagement\Listeners\Link\HandleLinkUpdated;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Link::created([
            HandleLinkCreated::class, 'handle',
        ]);

        Link::updated([
            HandleLinkUpdated::class, 'handle',
        ]);

        Link::softDeleted([
            HandleLinkSoftDeleted::class, 'handle',
        ]);

        Link::restored([
            HandleLinkRestored::class, 'handle',
        ]);

        Link::forceDeleted([
            HandleLinkForceDeleted::class, 'handle',
        ]);
    }
}
