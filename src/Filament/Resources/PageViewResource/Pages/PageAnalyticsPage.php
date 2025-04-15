<?php


declare(strict_types=1);

namespace Prajwal89\Analytics\Filament\Resources\PageViewResource\Pages;

use Filament\Resources\Pages\Page;
use Prajwal89\Analytics\Filament\Resources\PageViewResource;
use Prajwal89\Analytics\Filament\Resources\PageViewResource\Widgets\CountryViewsDoughnutChart;
use Prajwal89\Analytics\Filament\Resources\PageViewResource\Widgets\PageViewsDoughnutChart;

class PageAnalyticsPage extends Page
{
    protected static string $resource = PageViewResource::class;

    // protected static string $view = 'analytics::filament.resources.page-view-resource.pages.analytics';

    // public static string $view = 'filament.resources.pageview-resource.pages.page-analytics-page';
    // protected static string $view = 'analytics::filament.resources.pageview-resource.pages.index';


    public function getView(): string
    {
        return <<<'BLADE'

        BLADE;
    }
    protected function getFooterWidgets(): array
    {
        return [
            PageViewsDoughnutChart::class,
            CountryViewsDoughnutChart::class,
        ];
    }
}
