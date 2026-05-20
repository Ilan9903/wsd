<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Lomkit\Access\Controls\HasControl;

/**
 * @mixin IdeHelperTenantConfig
 */
class TenantConfig extends Model
{
    use HasControl;

    protected $fillable = [
        'isPasswordShouldRenew',
        'passwordExpirationDelay',
    ];

    protected $casts = [
        'isPasswordShouldRenew' => 'boolean',
    ];
}
