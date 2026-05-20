<?php

namespace App\Filament\Resources\Histories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->label('Description')
                    ->searchable(),
                TextColumn::make('action')
                    ->label('Action')
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('Address IP')
                    ->searchable(),
                TextColumn::make('link_id')
                    ->label('Link')
                    ->searchable(),
                TextColumn::make('user_id')
                    ->label('User')
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
