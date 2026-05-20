<?php

namespace App\Filament\Resources\Themes;

use App\Filament\Resources\Themes\Pages\CreateTheme;
use App\Filament\Resources\Themes\Pages\EditTheme;
use App\Filament\Resources\Themes\Pages\ListThemes;
use App\Filament\Resources\Themes\Pages\ViewTheme;
use App\Filament\Resources\Themes\Schemas\ThemeForm;
use App\Filament\Resources\Themes\Schemas\ThemeInfolist;
use App\Filament\Resources\Themes\Tables\ThemesTable;
use App\Models\Theme;
use App\Services\Tenant\TenantFilament;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ThemeResource extends Resource
{
    protected static ?string $model = Theme::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaintBrush;

    public static function form(Schema $schema): Schema
    {
        return ThemeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ThemeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ThemesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListThemes::route('/'),
            'create' => CreateTheme::route('/create'),
            'view' => ViewTheme::route('/{record}'),
            'edit' => EditTheme::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        TenantFilament::initializeTenant();
        $query = static::getModel()::query();

        if (! static::isScopedToTenant()) {
            $panel = Filament::getCurrentOrDefaultPanel();

            if ($panel?->hasTenancy()) {
                $query->withoutGlobalScope($panel->getTenancyScopeName());
            }
        }

        return $query;
    }
}
