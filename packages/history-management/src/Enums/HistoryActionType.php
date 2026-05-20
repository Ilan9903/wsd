<?php

namespace Hopla\HistoryManagement\Enums;

enum HistoryActionType: string
{
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case LINK_CREATED = 'link_created';
    case LINK_UPDATE = 'link_update';
    case LINK_SHARE = 'link_share';
    case LINK_UNSHARE = 'link_unshare';
    case LINK_EXPIRED = 'link_expired';
    case LINK_SOFT_DELETE = 'link_soft_delete';
    case LINK_RESTORE = 'link_restore';
    case LINK_FORCE_DELETE = 'link_force_delete';
}
