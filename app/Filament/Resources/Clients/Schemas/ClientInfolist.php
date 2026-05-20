<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Models\Client;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ClientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('global_id'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('name'),
                TextEntry::make('phone_number')
                    ->placeholder('-'),
                TextEntry::make('front_route')
                    ->placeholder('-'),
                TextEntry::make('address')
                    ->placeholder('-'),
                TextEntry::make('zipcode')
                    ->placeholder('-'),
                TextEntry::make('avatar')
                    ->placeholder('-'),
                TextEntry::make('tenant')
                    ->placeholder('-'),
                TextEntry::make('domain')
                    ->placeholder('-'),
                TextEntry::make('bucket')
                    ->placeholder('-'),
                TextEntry::make('used_storage')
                    ->numeric(),
                TextEntry::make('allocated_users')
                    ->numeric(),
                TextEntry::make('allocated_storage')
                    ->numeric(),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Client $record): bool => $record->trashed()),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
