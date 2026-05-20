<?php

namespace Hopla\HistoryManagement\Listeners\User;

use Hopla\HistoryManagement\Enums\HistoryActionType;
use Hopla\HistoryManagement\Handlers\LogManager;
use Illuminate\Auth\Events\Logout;

class HandleUserLogout
{
    public function __construct(
        private readonly LogManager $logManager
    ) {}

    public function handle(Logout $event): void
    {
        $user = $event->user;
        $action = HistoryActionType::LOGOUT;

        $description = $this->logManager->generateDescription($user, null, $action);
        $this->logManager->createdUserHistories($user, $action, $description);
    }
}
