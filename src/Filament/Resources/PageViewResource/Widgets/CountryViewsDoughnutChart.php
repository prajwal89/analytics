<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Filament\Resources\PageViewResource\Widgets;

use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Prajwal89\Analytics\Models\PageView;

class CountryViewsDoughnutChart extends ChartWidget
{
    protected static ?string $heading = 'Page Views By Countries';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        // Get the selected filter or default to "Today"
        $filter = $this->filter ?? 'Today';

        // Get the date range based on the filter
        [$startDate, $endDate] = $this->getDateRange($filter);

        // Get the page views for the selected period, grouped by country
        $views = PageView::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('country_code')
            ->groupBy('country_code')
            ->selectRaw('country_code, count(*) as views')
            ->orderByDesc('views')
            ->get();

        // Calculate total views for percentage calculation
        $totalViews = $views->sum('views');

        // Map and add percentage to label
        $views = $views->map(function (PageView $pageView) use ($totalViews) {
            $percentage = ($pageView->views / $totalViews) * 100;
            // Use country_code as label (you might want to map this to country names in a production app)
            $pageView->label = $pageView->country_code . ' (' . $pageView->views . ' - ' . number_format($percentage, 1) . '%)';

            return $pageView;
        });

        // Extract data for the chart
        $viewsData = $views->pluck('views')->toArray();
        $countryLabels = $views->pluck('label')->toArray();

        return [
            'datasets' => [
                [
                    'data' => $viewsData,
                    'backgroundColor' => $this->generateColors(count($viewsData)),
                    'borderColor' => '#ffffff',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $countryLabels,
        ];
    }

    protected function getOptions(): array|RawJs|null
    {
        return RawJs::make(<<<'JS'
            {
                animation: {
                    duration: 0,
                },
                elements: {
                    point: {
                        radius: 0,
                    },
                    hit: {
                        radius: 0,
                    },

                },
                maintainAspectRatio: false,
                borderRadius: 4,
                scaleBeginAtZero: true,
                radius: '85%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'left',
                        align: 'bottom',
                        labels: {
                            usePointStyle: true,
                            font: {
                                size: 10
                            }
                        }
                    },
                },
                scales: {
                    x: {
                        display: false,
                    },
                    y: {
                        display: false,
                    },
                },
                tooltips: {
                    enabled: false,
                },
            }
        JS);
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    /**
     * Generate distinct colors for the chart using HSL for better contrast.
     */
    private function generateColors(int $count): array
    {
        $colors = [];

        for ($i = 0; $i < $count; $i++) {
            $hue = ($i * 137) % 360; // Distribute hues evenly
            $colors[] = "hsl($hue, 70%, 50%)";
        }

        return $colors;
    }

    /**
     * Get the date range for the selected filter.
     */
    private function getDateRange(string $filter): array
    {
        switch ($filter) {
            case 'Yesterday':
                return [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()];
            case 'Same Day Last Week':
                return [
                    Carbon::now()->subWeek()->startOfDay(),
                    Carbon::now()->subWeek()->endOfDay(),
                ];
            case 'Last 30 Days':
                return [Carbon::now()->subDays(30)->startOfDay(), Carbon::now()->endOfDay()];
            case 'This Month':
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
            case 'All Time':
                return [Carbon::now()->subDecades(200), Carbon::now()];
            case 'Today':
            default:
                return [Carbon::today()->startOfDay(), Carbon::now()];
        }
    }

    protected function getFilters(): ?array
    {
        return [
            'Today' => 'Today',
            'Yesterday' => 'Yesterday',
            'Same Day Last Week' => 'Same Day Last Week',
            'Last 30 Days' => 'Last 30 Days',
            'This Month' => 'This Month',
            'All Time' => 'All Time',
        ];
    }
}
