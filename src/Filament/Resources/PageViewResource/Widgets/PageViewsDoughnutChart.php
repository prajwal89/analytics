<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Filament\Resources\PageViewResource\Widgets;

use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Prajwal89\Analytics\Models\PageView;

class PageViewsDoughnutChart extends ChartWidget
{
    protected static ?string $heading = 'Page Views By Routes';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        // Get the selected filter or default to "Today"
        $filter = $this->filter ?? 'Today';

        // Get the date range based on the filter
        [$startDate, $endDate] = $this->getDateRange($filter);

        // Get the page views for the selected period
        $views = PageView::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('route_name')
            ->groupBy('route_name')
            ->selectRaw('route_name, count(*) as views')
            ->orderByDesc('views')
            ->get();

        // Calculate total views for percentage calculation
        $totalViews = $views->sum('views');

        // Map and add percentage to label
        $views = $views->map(function (PageView $pageView) use ($totalViews) {
            $percentage = ($pageView->views / $totalViews) * 100;
            $pageView->label = $pageView->route_name . ' (' . $pageView->views . ' - ' . number_format($percentage, 1) . '%)';

            return $pageView;
        });

        // dd($views);

        // Extract data for the chart
        $viewsData = $views->pluck('views')->toArray();
        $routeNames = $views->pluck('label')->toArray();

        return [
            'datasets' => [
                [
                    'data' => $viewsData,
                    'backgroundColor' => $this->generateColors(count($viewsData)),
                    'borderColor' => '#ffffff',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $routeNames,
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

    // protected function getOptions(): array
    // {
    //     return [
    //         'maintainAspectRatio' => false,
    //         'plugins' => [
    //             'legend' => [
    //                 'display' => true,
    //                 'position' => 'bottom',
    //             ],
    //         ],
    //         'cutout' => '60%',
    //         'animation' => [
    //             'animateScale' => true,
    //             'animateRotate' => true,
    //         ],
    //     ];
    // }

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
