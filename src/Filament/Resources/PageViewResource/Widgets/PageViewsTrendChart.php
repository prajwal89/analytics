<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Filament\Resources\PageViewResource\Widgets;

use Illuminate\Database\Eloquent\Model;
use Prajwal89\Analytics\Filament\BaseTrendChartWidget;
use Prajwal89\Analytics\Models\PageView;

class PageViewsTrendChart extends BaseTrendChartWidget
{
    protected static string $modelFqn = PageView::class;

    protected static ?string $heading = 'Page Views Trend';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '280px';

    public ?string $filter = 'Daily';

    public ?Model $record = null;

    public ?string $viewable = null;

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // dd($this->record);
        // Base query for `DailyPageView` table
        $query = PageView::query();

        // Add conditions based on the filter
        $results = match ($this->filter) {
            'Monthly' => $query
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period, COUNT(*) as aggregate')
                ->whereBetween('created_at', [now()->subMonths(60)->startOfMonth(), now()->endOfMonth()])
                ->when($this->record, function ($query) {
                    return $query
                        ->where('viewable_id', $this->record->id)
                        ->where('viewable_type', get_class($this->record));
                })
                ->when($this->viewable, function ($query) {
                    return $query
                        ->where('viewable_type', $this->viewable);
                })
                ->groupBy('period')
                ->orderBy('period')
                ->get(),
            'Daily' => $query
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") as period, COUNT(*) as aggregate')
                ->whereBetween('created_at', [now()->subDays(60)->startOfDay(), now()->endOfDay()])
                ->when($this->record, function ($query) {
                    return $query
                        ->where('viewable_id', $this->record->id)
                        ->where('viewable_type', get_class($this->record));
                })
                ->when($this->viewable, function ($query) {
                    return $query
                        ->where('viewable_type', $this->viewable);
                })
                ->groupBy('period')
                ->orderBy('period')
                ->get(),
        };

        return [
            'datasets' => [
                [
                    'label' => 'Page Views',
                    'data' => $results->pluck('aggregate'),
                    // 'borderColor' => '#22c55e', // Green border color
                    // 'backgroundColor' => 'rgba(34, 197, 94, 0.2)', // Light green fill color
                    'tension' => 0.4,
                    'fill' => true, // Enables fill under the line
                ],
            ],
            'labels' => $results->pluck('period'),
        ];
    }

    public function getHeading(): string
    {
        if ($this->record instanceof Model) {
            return self::$heading . ' for ' . class_basename($this->record) . ':' . $this->record->getKey();
        }

        if ($this->viewable) {
            return self::$heading . ' for ' . class_basename($this->viewable);
        }

        return self::$heading;
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'Monthly' => 'Monthly',
            'Daily' => 'Daily',
        ];
    }
}
