<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperUserHasContact
 */
class UserHasContact extends Pivot
{
    /**
     * @return BelongsTo<User, Contact>
     */
    public function user(): BelongsTo
    {
        /** @var BelongsTo<User, Contact> */
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Contact, User>
     */
    public function contact(): BelongsTo
    {
        /** @var BelongsTo<Contact, User> */
        return $this->belongsTo(Contact::class);
    }
}
