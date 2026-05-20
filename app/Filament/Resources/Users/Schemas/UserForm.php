<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('city'),
                TextInput::make('country'),
                TextInput::make('phone')
                    ->tel(),
                DateTimePicker::make('email_verified_at'),
                FileUpload::make('image')
                    ->image(),
                Select::make('groups')
                    ->label('Groups')
                    ->multiple()
                    ->relationship('groups')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->searchable()
                    ->preload(),

            ]);
    }
}
