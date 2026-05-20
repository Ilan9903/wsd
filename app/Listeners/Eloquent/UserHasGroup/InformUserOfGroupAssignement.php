<?php

namespace App\Listeners\Eloquent\UserHasGroup;

use App\Models\UserHasGroup;
use App\Notifications\InformUserOfGroupAssignement as InformUserOfGroupAssignementNotification;

class InformUserOfGroupAssignement
{
    /**
     * Handle the event.
     */
    public function handle(UserHasGroup $userHasGroup): void
    {
        $user = $userHasGroup->loadMissing('user')->user;
        $groupName = $userHasGroup->loadMissing('group')->group->name;

        $user->notify(new InformUserOfGroupAssignementNotification($groupName));
    }
}
