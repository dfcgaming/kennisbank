<?php

namespace App\Filament\Resources\Assets;

use App\Enums\AssetType;
use App\Filament\RelationManagers\ArticlesRelationManager;
use App\Filament\Resources\Assets\Pages\CreateAsset;
use App\Filament\Resources\Assets\Pages\EditAsset;
use App\Filament\Resources\Assets\Pages\ListAssets;
use App\Models\Asset;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Asset details')
                    ->schema([
                        TextInput::make('name')->required()->maxLength(255),
                        Select::make('type')->options(AssetType::class)->required()->live(),
                        TextInput::make('equipment_kind')
                            ->label('Equipment kind')
                            ->datalist(['Phone', 'Monitor', 'Mouse', 'Keyboard', 'Headset', 'Docking station'])
                            ->maxLength(255)
                            ->visible(fn (Get $get): bool => in_array($get('type'), [AssetType::PersonalEquipment, AssetType::PersonalEquipment->value], true)),
                        TextInput::make('asset_tag')->maxLength(255)->unique(),
                        TextInput::make('serial_number')->maxLength(255),
                        TextInput::make('manufacturer')->maxLength(255),
                        TextInput::make('model')->maxLength(255),
                        TextInput::make('location')->maxLength(255),
                    ])->columns(2)->columnSpanFull(),
                Section::make('Assignment')
                    ->schema([
                        Select::make('person_id')
                            ->label('Assigned to')
                            ->relationship('person', 'name')
                            ->searchable(['name', 'email'])
                            ->placeholder('Unassigned'),
                    ])->columnSpanFull(),
                Section::make('Server details')
                    ->schema([
                        TextInput::make('hostname')->maxLength(255),
                        TextInput::make('ip_address')->label('IP address')->ip(),
                    ])->columns(2)->columnSpanFull()
                    ->visible(fn (Get $get): bool => in_array($get('type'), [AssetType::Server, AssetType::Server->value], true)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->emptyStateHeading('No assets yet')
            ->emptyStateDescription('Add a laptop, personal equipment, or a server.')
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('type')->badge()->sortable(),
                TextColumn::make('equipment_kind')->label('Equipment kind')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('asset_tag')->searchable()->placeholder('—'),
                TextColumn::make('person.name')->label('Assigned to')->searchable()->sortable()->placeholder('Unassigned'),
                TextColumn::make('serial_number')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('hostname')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('articles_count')->counts('articles')->label('Documents')->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')->options(AssetType::class),
                SelectFilter::make('person')->label('Assigned to')->relationship('person', 'name')->searchable(),
                Filter::make('unassigned')->query(fn (Builder $query): Builder => $query->whereNull('person_id')),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ArticlesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssets::route('/'),
            'create' => CreateAsset::route('/create'),
            'edit' => EditAsset::route('/{record}/edit'),
        ];
    }
}
