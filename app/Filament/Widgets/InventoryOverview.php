<?php

namespace App\Filament\Widgets;

use App\Enums\AssetType;
use App\Filament\Resources\Assets\AssetResource;
use App\Filament\Resources\KnowledgeArticles\KnowledgeArticleResource;
use App\Filament\Resources\People\PersonResource;
use App\Models\Asset;
use App\Models\KnowledgeArticle;
use App\Models\Person;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'IT inventory';

    protected function getStats(): array
    {
        return [
            Stat::make('Assets', Asset::count())
                ->icon(Heroicon::OutlinedComputerDesktop)->url(AssetResource::getUrl()),
            Stat::make('Servers', Asset::where('type', AssetType::Server)->count())
                ->icon(Heroicon::OutlinedServer)
                ->url(AssetResource::getUrl(parameters: ['tableFilters' => ['type' => ['value' => AssetType::Server->value]]])),
            Stat::make('People', Person::count())
                ->icon(Heroicon::OutlinedUsers)->url(PersonResource::getUrl()),
            Stat::make('Documents', KnowledgeArticle::count())
                ->icon(Heroicon::OutlinedBookOpen)->url(KnowledgeArticleResource::getUrl()),
        ];
    }
}
