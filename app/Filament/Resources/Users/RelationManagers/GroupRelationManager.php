<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\AttachAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GroupRelationManager extends RelationManager
{
    protected static string $relationship = 'groups';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $relatedResource = UserResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
            ])
            ->headerActions([
                AttachAction::make(),
            ]);
    }
}
