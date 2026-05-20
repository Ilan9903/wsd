<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('phone_number')
                    ->tel(),
                TextInput::make('address'),
                TextInput::make('zipcode'),
                TextInput::make('allocated_users')
                    ->required()
                    ->numeric()
                    ->default(10),
                TextInput::make('allocated_storage')
                    ->required()
                    ->numeric()
                    ->default(10),
            ]);
    }
}
