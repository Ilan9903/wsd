<?php

namespace App\Filament\Resources\Themes\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ThemeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                ColorPicker::make('background'),
                ColorPicker::make('primary_color'),
                ColorPicker::make('secondary_color'),
                ColorPicker::make('button_primary_color'),
                ColorPicker::make('button_secondary_color'),
                ColorPicker::make('button_text_primary_color'),
                ColorPicker::make('button_text_secondary_color'),
                ColorPicker::make('text_primary_color'),
                ColorPicker::make('text_secondary_color'),
                FileUpload::make('logo_light_theme'),
                FileUpload::make('logo_dark_theme'),
                Toggle::make('status')
                    ->required(),
            ]);
    }
}
