<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperUserHasGroup
 */
class UserHasGroup extends Pivot
{
    public $timestamps = false;

    /**
     * @return BelongsTo<User, Group>
     */
    public function user(): BelongsTo
    {
        /** @var BelongsTo<User, Group> */
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<User, Group>
     */
    public function group(): BelongsTo
    {
        /** @var BelongsTo<User, Group> */
        return $this->belongsTo(Group::class);
    }
}
