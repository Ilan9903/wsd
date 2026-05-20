<?php

namespace App\Filament\Resources\Histories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class HistoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('description')
                    ->label('Description'),
                TextEntry::make('action')
                    ->label('Action'),
                TextEntry::make('ip_address')
                    ->label('Address IP'),
                TextEntry::make('link_id')
                    ->label('Link'),
                TextEntry::make('user_id')
                    ->label('User'),
            ]);
    }
}
