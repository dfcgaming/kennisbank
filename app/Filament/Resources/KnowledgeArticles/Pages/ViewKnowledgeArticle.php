<?php

namespace App\Filament\Resources\KnowledgeArticles\Pages;

use App\Filament\Resources\KnowledgeArticles\KnowledgeArticleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKnowledgeArticle extends ViewRecord
{
    protected static string $resource = KnowledgeArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
