<?php

namespace App\Livewire\Tenants;

use App\Models\User;
use App\Services\Tenant\TenantFilament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class UserTable extends TableWidget
{
    public static function canView(): bool
    {
        return TenantFilament::availableForNavigation();
    }

    public function table(Table $table): Table
    {
        TenantFilament::initializeTenant();

        return $table
            ->defaultPaginationPageOption(5)
            ->query(fn (): Builder => User::query())
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('email')
                    ->searchable(),

                TextColumn::make('used_storage')
                    ->label('Stockage utilisé')
                    ->formatStateUsing(fn ($state) => round(($state ?? 0) / 1024 ** 3, 2).' Go')
                    ->sortable(),

            ]);
    }
}
