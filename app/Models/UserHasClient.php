<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperUserHasClient
 */
class UserHasClient extends Pivot
{
    /**
     * @return BelongsTo<User, UserHasClient>
     */
    public function user(): BelongsTo
    {
        /** @var BelongsTo<User, UserHasClient> $relation */
        $relation = $this->belongsTo(User::class);

        return $relation;
    }

    /**
     * @return BelongsTo<Client, UserHasClient>
     */
    public function client(): BelongsTo
    {
        /** @var BelongsTo<Client, UserHasClient> $relation */
        $relation = $this->belongsTo(Client::class);

        return $relation;
    }
}
