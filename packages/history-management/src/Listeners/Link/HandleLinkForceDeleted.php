<?php

namespace Hopla\HistoryManagement\Listeners\Link;

use App\Models\Link;
use Hopla\HistoryManagement\Enums\HistoryActionType;
use Hopla\HistoryManagement\Handlers\LogManager;

class HandleLinkForceDeleted
{
    public function __construct(
        private readonly LogManager $logManager
    ) {}

    public function handle(
        Link $link
    ): void {
        $user = auth()->user();
        $action = HistoryActionType::LINK_FORCE_DELETE;

        $description = $this->logManager->generateDescription($user, $link, $action);
        $this->logManager->createdLinkHistories($user, $link, $action, $description);
    }
}
