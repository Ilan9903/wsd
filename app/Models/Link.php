<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Lomkit\Access\Controls\HasControl;

/**
 * #@mixin contains unknown class App\\Models\\IdeHelperLink#
 *
 * @mixin IdeHelperLink
 */
class Link extends Model
{
    use HasControl, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'id',
        'url',
        'is_active',
        'expired_at',
        'user_id',
        'has_receipt',
        'has_watermark',
        'recipients_email_addresses',
        'message_subject',
        'message',
        'src_folder_id',
    ];

    protected $hidden = [
        'password',
    ];

    public function casts(): array
    {
        return [
            'password' => 'hashed',
            'expired_at' => 'datetime',
            'recipients_email_addresses' => 'array',
        ];
    }

    /**
     * @return HasOne<Link, User>
     */
    public function user(): HasOne
    {
        /** @var HasOne<Link, User> */
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return bool
     */
    public function hasPassword(): bool
    {
        return ! empty($this->password);
    }

    /**
     * @param  string|null  $password
     * @return bool
     */
    public function checkPassword(?string $password): bool
    {
        return $this->password && Hash::check($password, $this->password);
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if ($this->expired_at <= now()->subDays(30)) {
            $this->update(['is_active' => false]);

            return false;
        }

        return true;
    }

    /**
     * @return HasMany<Link, FileOrFolder>
     */
    public function fileOrFolder(): HasMany
    {
        /** @var HasMany<Link, FileOrFolder> */
        return $this->hasMany(FileOrFolder::class);
    }
}
