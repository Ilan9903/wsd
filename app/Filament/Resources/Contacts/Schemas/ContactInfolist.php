<?php

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContactInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('first_name')
                    ->label('First Name'),
                TextEntry::make('last_name')
                    ->label('Last Name'),
                TextEntry::make('email')
                    ->label('Email address'),
            ]);
    }
}
