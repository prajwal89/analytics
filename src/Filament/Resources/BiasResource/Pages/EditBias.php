<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Filament\Resources\BiasResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Prajwal89\Analytics\Filament\Resources\BiasResource;

class EditBias extends EditRecord
{
    protected static string $resource = BiasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
