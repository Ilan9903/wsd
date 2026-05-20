<?php

namespace App\Models;

use App\Enums\FileOrFolderType;
use App\Services\Minio\Bucket;
use Database\Factories\FileOrFolderFactory;
use Hopla\FileOrFolderManagement\Handlers\PathBuilder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lomkit\Access\Controls\HasControl;

/**
 * @mixin IdeHelperFileOrFolder
 */
class FileOrFolder extends Model
{
    /** @use HasFactory<FileOrFolderFactory> */
    use HasControl, HasFactory, HasUuids;

    use SoftDeletes {
        forceDelete as private traitForceDelete;
    }

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'parent_id',
        'size',
        'delete_at',
        'type',
        'path',
        'user_id',
        'mime_type',
        'link_id',
    ];

    protected $casts = [
        'type' => FileOrFolderType::class,
    ];

    /**
     * @return HasMany<FileOrFolder, $this>
     */
    public function childrens(): HasMany
    {
        return $this->hasMany(FileOrFolder::class, 'parent_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<FileOrFolder, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(FileOrFolder::class, 'parent_id');
    }

    /**
     * @return HasOne<FileOrFolder, Link>
     */
    public function link(): HasOne
    {
        /** @var HasOne<FileOrFolder, Link> */
        return $this->hasOne(Link::class, 'id', 'link_id');
    }

    public function forceDelete(): ?bool
    {
        (new Bucket)->getBucket();

        if ($this->type !== FileOrFolderType::Folder) {
            $disk = \Storage::disk('minio');
            $disk->delete(PathBuilder::buildMinioFullPath($this));
        }

        return $this->traitForceDelete();
    }
}
