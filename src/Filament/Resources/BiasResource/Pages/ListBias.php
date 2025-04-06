<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Filament\Resources\BiasResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Prajwal89\Analytics\Filament\Resources\BiasResource;
use Prajwal89\Analytics\Filament\Resources\BiasResource\Widgets\BiasTrendChart;

class ListBias extends ListRecords
{
    protected static string $resource = BiasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            BiasTrendChart::class,
        ];
    }
}
