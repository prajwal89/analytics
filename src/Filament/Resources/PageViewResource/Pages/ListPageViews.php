<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Filament\Resources\PageViewResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Prajwal89\Analytics\Filament\Resources\PageViewResource;
use Prajwal89\Analytics\Filament\Resources\PageViewResource\Widgets\PageViewsTrendChart;

class ListPageViews extends ListRecords
{
    protected static string $resource = PageViewResource::class;

    // public static string $view = 'analytics::filament.resources.pageview-resource.pages.index';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            PageViewsTrendChart::class,
        ];
    }
}
