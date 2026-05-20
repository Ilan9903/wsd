<?php

namespace App\Filament\Resources\Themes\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ThemeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),

                ColorEntry::make('background')
                    ->copyable()
                    ->copyMessage('Copied!')
                    ->copyMessageDuration(1500),

                ColorEntry::make('primary_color')
                    ->copyable()
                    ->copyMessage('Copied!')
                    ->copyMessageDuration(1500),

                ColorEntry::make('secondary_color'),
                ColorEntry::make('button_primary_color'),
                ColorEntry::make('button_secondary_color'),
                ColorEntry::make('button_text_primary_color'),
                ColorEntry::make('button_text_secondary_color'),
                ColorEntry::make('text_primary_color'),
                ColorEntry::make('text_secondary_color'),

                ImageEntry::make('logo_light_theme')->placeholder('-'),
                ImageEntry::make('logo_dark_theme')->placeholder('-'),

                IconEntry::make('status')->boolean(),

                TextEntry::make('created_at')->dateTime()->placeholder('-'),
                TextEntry::make('updated_at')->dateTime()->placeholder('-'),
            ]);
    }
}
