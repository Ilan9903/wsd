<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rules\Contains;
use Lomkit\Access\Controls\HasControl;

/**
 * #@mixin contains unknown class App\\Models\\IdeHelperGroup#
 *
 * @mixin IdeHelperGroup
 */
class Group extends Model
{
    use HasControl, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'owner_id',
        'created_at',
    ];

    /** @return BelongsToMany<User, UserHasGroup> */
    public function users(): BelongsToMany
    {
        /** @var BelongsToMany<User, UserHasGroup> */
        return $this
            ->belongsToMany(User::class, 'user_has_group');
    }

    /** @return BelongsTo<User, UserHasGroup> */
    public function owner(): BelongsTo
    {
        /** @var BelongsTo<User, UserHasGroup> */
        return $this
            ->belongsTo(User::class, 'owner_id');
    }
}
