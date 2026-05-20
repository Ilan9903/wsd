<?php

namespace App\Filament\Resources\Histories;

use App\Filament\Resources\Histories\Pages\ListHistories;
use App\Filament\Resources\Histories\Pages\ViewHistory;
use App\Filament\Resources\Histories\RelationManagers\UsersRelationManager;
use App\Filament\Resources\Histories\Schemas\HistoryInfolist;
use App\Filament\Resources\Histories\Tables\HistoriesTable;
use App\Models\History;
use App\Services\Tenant\TenantFilament;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HistoryResource extends Resource
{
    protected static ?string $model = History::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function infolist(Schema $schema): Schema
    {
        return HistoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HistoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHistories::route('/'),
            'view' => ViewHistory::route('/{record}'),
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
