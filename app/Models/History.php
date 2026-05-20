<?php

namespace App\Models;

use Database\Factories\HistoryFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Contains;
use Lomkit\Access\Controls\HasControl;

/**
 * * #@mixin contains unknown class App\\Models\\IdeHelperHistory#
 *
 * @mixin IdeHelperHistory
 */
class History extends Model
{
    /** @use HasFactory<HistoryFactory> */
    use HasControl,HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'user_id',
        'link_id',
        'model_type',
        'action',
        'description',
        'ip_address',
    ];
}
