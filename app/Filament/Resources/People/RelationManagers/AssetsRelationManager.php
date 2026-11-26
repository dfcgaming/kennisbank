<?php

namespace App\Filament\Resources\People\RelationManagers;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\Asset;
use Filament\Actions\AssociateAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'assets';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('type')->badge(),
                TextColumn::make('asset_tag')->searchable(),
            ])
            ->headerActions([
                AssociateAction::make()
                    ->label('Assign existing asset')
                    ->recordSelectSearchColumns(['name', 'asset_tag'])
                    ->recordSelectOptionsQuery(fn (Builder $query): Builder => $query->whereNull('person_id')),
            ])
            ->recordActions([
                EditAction::make()->url(fn (Asset $record): string => AssetResource::getUrl('edit', ['record' => $record])),
                DissociateAction::make()->label('Unassign'),
            ]);
    }
}
