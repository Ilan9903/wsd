<?php

namespace App\Filament\Resources\Links\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ToggleColumn;

class LinkInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextEntry::make('url')->label('URL'),
            ToggleColumn::make('is_active')->label('Actif'),
            TextEntry::make('expired_at')->label('Expiration'),
            TextEntry::make('password')->label('Mot de passe'),
            TextEntry::make('user.email')->label('Utilisateur'),
            TextEntry::make('created_at')->label('Créé le'),
            TextEntry::make('updated_at')->label('Mis à jour le'),
        ]);
    }
}
