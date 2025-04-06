<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Filament\Resources\BiasResource\Widgets;

use Prajwal89\Analytics\Filament\BaseTrendChartWidget;
use Prajwal89\Analytics\Models\Bias;

class BiasTrendChart extends BaseTrendChartWidget
{
    protected static ?string $heading = 'Bias';

    protected static string $modelFqn = Bias::class;
}
