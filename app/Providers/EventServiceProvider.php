<?php

namespace App\Providers;

use App\Listeners\Eloquent\Client\HandleClientCreated;
use App\Listeners\Eloquent\Contact\HandleContactCreating;
use App\Listeners\Eloquent\User\SendUserCreatedNotification;
use App\Listeners\Eloquent\UserHasGroup\InformUserOfGroupAssignement;
use App\Models\Client;
use App\Models\Contact;
use App\Models\User;
use App\Models\UserHasGroup;
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
        User::created([
            SendUserCreatedNotification::class, 'handle',
        ]);

        UserHasGroup::created([
            InformUserOfGroupAssignement::class, 'handle',
        ]);

        Client::created([
            HandleClientCreated::class, 'handle',
        ]);

        Contact::creating([
            HandleContactCreating::class, 'handle',
        ]);
    }
}
