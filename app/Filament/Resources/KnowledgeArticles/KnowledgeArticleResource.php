<?php

namespace App\Filament\Resources\KnowledgeArticles;

use App\Filament\Resources\KnowledgeArticles\Pages\CreateKnowledgeArticle;
use App\Filament\Resources\KnowledgeArticles\Pages\EditKnowledgeArticle;
use App\Filament\Resources\KnowledgeArticles\Pages\ListKnowledgeArticles;
use App\Filament\Resources\KnowledgeArticles\Pages\ViewKnowledgeArticle;
use App\Models\KnowledgeArticle;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KnowledgeArticleResource extends Resource
{
    protected static ?string $model = KnowledgeArticle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'Documentation';

    protected static ?string $slug = 'documentation';

    protected static ?string $modelLabel = 'document';

    protected static ?string $pluralModelLabel = 'Documentation';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Document')->schema(self::documentFields())->columnSpanFull(),
                Section::make('Linked records')
                    ->description('Leave these empty for a global document.')
                    ->schema([
                        Select::make('assets')->multiple()->relationship('assets', 'name')->searchable(['name', 'asset_tag']),
                        Select::make('people')->multiple()->relationship('people', 'name')->searchable(['name', 'email']),
                    ])->columns(2)->columnSpanFull(),
            ]);
    }

    public static function documentFields(): array
    {
        return [
            TextInput::make('title')->required()->maxLength(255),
            MarkdownEditor::make('body')
                ->label('Content')
                ->toolbarButtons(['bold', 'italic', 'heading', 'link', 'blockquote', 'codeBlock', 'bulletList', 'orderedList', 'undo', 'redo'])
                ->required(),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Document')
                    ->schema([TextEntry::make('body')->hiddenLabel()->markdown()])
                    ->columnSpanFull(),
                Section::make('Linked records')
                    ->schema([
                        TextEntry::make('assets.name')->label('Assets')->listWithLineBreaks()->placeholder('None'),
                        TextEntry::make('people.name')->label('People')->listWithLineBreaks()->placeholder('None'),
                    ])->columns(2)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->emptyStateHeading('No documentation yet')
            ->emptyStateDescription('Write a global guide or link a document to assets and people.')
            ->columns([
                TextColumn::make('title')->searchable(['title', 'body'])->sortable(),
                TextColumn::make('assets_count')->counts('assets')->label('Assets')->sortable(),
                TextColumn::make('people_count')->counts('people')->label('People')->sortable(),
                TextColumn::make('updated_at')->label('Last edited')->since()->sortable(),
            ])
            ->filters([
                Filter::make('global')->label('Global documents')
                    ->query(fn (Builder $query): Builder => $query->doesntHave('assets')->doesntHave('people')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKnowledgeArticles::route('/'),
            'create' => CreateKnowledgeArticle::route('/create'),
            'view' => ViewKnowledgeArticle::route('/{record}'),
            'edit' => EditKnowledgeArticle::route('/{record}/edit'),
        ];
    }
}
