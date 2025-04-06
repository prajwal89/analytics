<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Filament\Resources\PageViewResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Prajwal89\Analytics\Filament\Resources\PageViewResource;

class EditPageView extends EditRecord
{
    protected static string $resource = PageViewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
