<?php

namespace Hopla\HistoryManagement\Handlers;

use App\Models\Link;
use App\Models\User;
use Hopla\HistoryManagement\Enums\HistoryActionType;
use Hopla\HistoryManagement\Enums\HistoryModelType;
use Hopla\HistoryManagement\Jobs\LogHistoriesJob;

class LogManager
{
    public function generateDescription(User $user, ?Link $link, HistoryActionType $action): string
    {
        switch ($action) {
            case HistoryActionType::LOGIN:
                return __('log.login-success', ['first_name' => $user->first_name, 'last_name' => $user->last_name]);
            case HistoryActionType::LOGOUT:
                return __('log.logout-success', ['first_name' => $user->first_name, 'last_name' => $user->last_name]);
            case HistoryActionType::LINK_CREATED:
                return __('log.created-link', ['first_name' => $user->first_name, 'last_name' => $user->last_name, 'link_url' => $link->url]);
            case HistoryActionType::LINK_UPDATE:
                return __('log.update-link', ['first_name' => $user->first_name, 'last_name' => $user->last_name, 'link_url' => $link->url]);
            case HistoryActionType::LINK_SHARE:
                return __('log.shared-link', ['first_name' => $user->first_name, 'last_name' => $user->last_name, 'link_url' => $link->url]);
            case HistoryActionType::LINK_UNSHARE:
                return __('log.stop-sharing-link', ['first_name' => $user->first_name, 'last_name' => $user->last_name, 'link_url' => $link->url]);
            case HistoryActionType::LINK_EXPIRED:
                return __('log.expired-link', ['first_name' => $user->first_name, 'last_name' => $user->last_name, 'link_url' => $link->url]);
            case HistoryActionType::LINK_SOFT_DELETE:
                return __('log.remove-link', ['first_name' => $user->first_name, 'last_name' => $user->last_name, 'link_url' => $link->url]);
            case HistoryActionType::LINK_RESTORE:
                return __('log.restore-link', ['first_name' => $user->first_name, 'last_name' => $user->last_name, 'link_url' => $link->url]);
            case HistoryActionType::LINK_FORCE_DELETE:
                return __('log.delete-link-trash', ['first_name' => $user->first_name, 'last_name' => $user->last_name, 'link_url' => $link->url]);
            default:
                return __('log.action-undefined');
        }
    }

    public function createdUserHistories(
        User $user,
        HistoryActionType $action,
        string $description,
    ): void {
        \Log::info('dispatch');
        LogHistoriesJob::dispatch(
            $user,
            null,
            HistoryModelType::USER,
            $action,
            $description,
            request()->ip()
        )->onQueue('logs');
    }

    public function createdLinkHistories(
        User $user,
        Link $link,
        HistoryActionType $action,
        string $description
    ): void {
        \Log::info('dispatch');
        LogHistoriesJob::dispatch(
            $user,
            $link,
            HistoryModelType::LINK,
            $action,
            $description,
            request()->ip()
        )->onQueue('logs');
    }
}
