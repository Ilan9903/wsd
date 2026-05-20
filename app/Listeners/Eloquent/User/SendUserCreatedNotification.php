<?php

namespace App\Listeners\Eloquent\User;

use App\Models\User;
use App\Notifications\UserCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendUserCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(User $user): void
    {
        $user->notify(new UserCreatedNotification($user));
    }
}
