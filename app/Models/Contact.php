<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rules\Contains;
use Laravel\Scout\Searchable;
use Lomkit\Access\Controls\HasControl;

/**
 * * #@mixin contains unknown class App\\Models\\IdeHelperContact#
 *
 * @mixin IdeHelperContact
 */
class Contact extends Model
{
    use HasControl, HasFactory, HasUuids, Searchable, SoftDeletes;

    protected $table = 'contacts';

    protected $fillable = [
        'id',
        'last_name',
        'first_name',
        'email',
        'created_at',
    ];

    /**
     * @return BelongsToMany<Contact, User>
     */
    public function users(): BelongsToMany
    {
        /** @var BelongsToMany<Contact, User> */
        return $this->belongsToMany(User::class, 'user_has_contact')->using(UserHasContact::class);
    }
}
