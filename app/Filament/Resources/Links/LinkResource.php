<?php

namespace App\Filament\Resources\Links;

use App\Filament\Resources\Links\Pages\ListLinks;
use App\Filament\Resources\Links\Pages\ViewLink;
use App\Filament\Resources\Links\Schemas\LinkInfolist;
use App\Filament\Resources\Links\Tables\LinksTable;
use App\Models\Link;
use App\Services\Tenant\TenantFilament;
use Filament\Exceptions\NoDefaultPanelSetException;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Lomkit\Access\Controls\HasControl;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

class LinkResource extends Resource
{
    use HasControl;

    protected static ?string $model = Link::class;

    public static function infolist(Schema $schema): Schema
    {
        return LinkInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LinksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLinks::route('/'),
            'view' => ViewLink::route('/{record}'),

        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById|NoDefaultPanelSetException
     */
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

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record = null): bool
    {
        return false;
    }
}
