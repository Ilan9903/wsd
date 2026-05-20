<?php

namespace App\Models;

use App\Notifications\TwoFactorActivationNotification;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lomkit\Access\Controls\HasControl;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements FilamentUser, JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasControl, HasFactory, HasRoles, HasUuids, Notifiable, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'last_name',
        'first_name',
        'email',
        'image',
        'city',
        'country',
        'phone',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_verified_at',
        'password_last_update_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return BelongsToMany<User, Group>
     */
    public function groups(): BelongsToMany
    {
        /** @var BelongsToMany<User, Group> */
        return $this->belongsToMany(Group::class, 'user_has_group');
    }

    /**
     * @return HasMany<Contact, $this>
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * @return HasMany<User, Link>
     */
    public function links(): HasMany
    {
        /** @var HasMany<User, Link> */
        return $this->hasMany(Link::class, 'user_id', 'id');
    }

    /**
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array<int>
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'user_id' => $this->getJWTIdentifier(),
        ];
    }

    /**
     * @return BelongsToMany<User, Client>
     */
    public function clients(): BelongsToMany
    {
        /** @var BelongsToMany<User, Client> */
        return $this->belongsToMany(Client::class, 'user_has_client');
    }

    public function getNameAttribute(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function canAccessPanel(Panel $panel): bool
    {

        return $this->can('view_users');
    }

    public function sendTwoFactorActivationNotification(string $token): void
    {
        $this->notify(new TwoFactorActivationNotification($token));
    }
}
