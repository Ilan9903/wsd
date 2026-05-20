<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class CustomEditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('first_name')
                    ->label('Prénom')
                    ->required()
                    ->maxLength(255),

                TextInput::make('last_name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Adresse email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->confirmed('email_confirmation'),

                TextInput::make('email_confirmation')
                    ->label('Confirmer l\'email')
                    ->email(),

                TextInput::make('current_password')
                    ->password()
                    ->label('Ancien mot de passe')
                    ->required(fn ($get) => filled($get('password')))
                    ->dehydrateStateUsing(fn ($state) => null)
                    ->rule(fn () => function ($attribute, $value, $fail) {
                        if (! Hash::check($value, auth()->user()->password)) {
                            $fail('L\'ancien mot de passe est incorrect.');
                        }
                    }),

                TextInput::make('password')
                    ->password()
                    ->label('Nouveau mot de passe')
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->confirmed('password_confirmation'),

                TextInput::make('password_confirmation')
                    ->password()
                    ->label('Confirmer le nouveau mot de passe'),
            ]);
    }
}
