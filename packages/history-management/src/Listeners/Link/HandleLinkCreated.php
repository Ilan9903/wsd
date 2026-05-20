<?php

namespace Hopla\HistoryManagement\Listeners\Link;

use App\Models\Link;
use Hopla\HistoryManagement\Enums\HistoryActionType;
use Hopla\HistoryManagement\Handlers\LogManager;

class HandleLinkCreated
{
    public function __construct(
        private readonly LogManager $logManager
    ) {}

    public function handle(
        Link $link
    ): void {
        $user = $link->user ?? auth()->user();
        $action = HistoryActionType::LINK_CREATED;

        $description = $this->logManager->generateDescription($user, $link, $action);
        $this->logManager->createdLinkHistories($user, $link, $action, $description);
    }
}
