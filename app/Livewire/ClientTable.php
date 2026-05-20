<?php

namespace App\Livewire;

use App\Models\Client;
use App\Services\Tenant\TenantFilament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ClientTable extends TableWidget
{
    public static function canView(): bool
    {
        return ! TenantFilament::availableForNavigation();
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(5)
            ->query(fn (): Builder => Client::query())
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('used_storage')
                    ->label('Stockage (utilisé / alloué)')
                    ->formatStateUsing(fn (int $state, $record) => sprintf(
                        '%s Go / %s Go',
                        round($state / 1024 ** 3, 2),
                        $record->allocated_storage,
                    ))
                    ->sortable(),

                TextColumn::make('domain')
                    ->label('Domain')
                    ->formatStateUsing(fn () => 'Go dashboard client')
                    ->url(fn ($record) => "https://{$record->domain}/support")
                    ->openUrlInNewTab()
                    ->toggleable(),
            ]);
    }
}
