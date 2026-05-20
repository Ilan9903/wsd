<?php

namespace Hopla\HistoryManagement\Listeners\User;

use Hopla\HistoryManagement\Enums\HistoryActionType;
use Hopla\HistoryManagement\Handlers\LogManager;
use Illuminate\Auth\Events\Login;

class HandleUserLogin
{
    public function __construct(
        private readonly LogManager $logManager
    ) {}

    public function handle(Login $event): void
    {
        $user = $event->user;
        $action = HistoryActionType::LOGIN;

        $description = $this->logManager->generateDescription($user, null, $action);
        $this->logManager->createdUserHistories($user, $action, $description);
    }
}
