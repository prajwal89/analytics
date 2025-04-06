<?php

declare(strict_types=1);

namespace Prajwal89\Analytics;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Prajwal89\Analytics\Filament\Resources\BiasResource;
use Prajwal89\Analytics\Filament\Resources\PageViewResource;

class AnalyticPlugin implements Plugin
{
    public function getId(): string
    {
        return 'analytics';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                PageViewResource::class,
                BiasResource::class,
            ])
            ->pages([
                // Settings::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
