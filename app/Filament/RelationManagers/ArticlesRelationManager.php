<?php

namespace App\Filament\RelationManagers;

use App\Filament\Resources\KnowledgeArticles\KnowledgeArticleResource;
use App\Models\KnowledgeArticle;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ArticlesRelationManager extends RelationManager
{
    protected static string $relationship = 'articles';

    protected static ?string $title = 'Documentation';

    public function form(Schema $schema): Schema
    {
        return $schema->components(KnowledgeArticleResource::documentFields());
    }

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel('document')
            ->recordTitleAttribute('title')
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('updated_at')->label('Last edited')->since(),
            ])
            ->headerActions([
                CreateAction::make(),
                AttachAction::make()->label('Link existing document'),
            ])
            ->recordActions([
                ViewAction::make()->url(fn (KnowledgeArticle $record): string => KnowledgeArticleResource::getUrl('view', ['record' => $record])),
                EditAction::make(),
                DetachAction::make()->label('Unlink'),
            ]);
    }
}
