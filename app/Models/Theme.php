<?php

namespace App\Models;

use Database\Factories\ThemeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lomkit\Access\Controls\HasControl;

/**
 * @mixin IdeHelperTheme
 */
class Theme extends Model
{
    /** @use HasFactory<ThemeFactory> */
    use HasControl, HasFactory;

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'background',
        'primary_color',
        'secondary_color',
        'button_primary_color',
        'button_secondary_color',
        'button_text_primary_color',
        'button_text_secondary_color',
        'text_primary_color',
        'text_secondary_color',
        'logo_light_theme',
        'logo_dark_theme',
        'status',
    ];

    /**
     * @param  int  $themeId
     * @return void
     */
    public static function activateTheme(int $themeId): void
    {
        self::where('status', true)->update(['status' => false]);

        $theme = self::find($themeId);
        if ($theme) {
            $theme->status = true;
            $theme->save();
        }
    }

    /**
     * @return void
     */
    public function deactivate(): void
    {
        $this->status = false;
        $this->save();
    }
}
