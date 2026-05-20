<?php

namespace App\Filament\Resources\Groups\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('owner_id')
                    ->relationship('owner')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->required(),
                Select::make('users')
                    ->label('Utilisateurs')
                    ->multiple()
                    ->relationship('users')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->searchable()
                    ->preload(),
            ]);
    }
}
