<?php

declare(strict_types=1);

namespace Prajwal89\Analytics;

use Illuminate\Support\ServiceProvider;
use Prajwal89\Analytics\Commands\SvgStorageLinkCommand;
use Prajwal89\Analytics\Commands\SyncGeoLiteDbCommand;
use Prajwal89\Analytics\Interfaces\RouteModelResolverInterface;
use Prajwal89\Analytics\Services\DefaultModelRouteResolver;

class AnalyticServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(RouteModelResolverInterface::class, function ($app) {
            // $resolverClass = config('analytics.route_resolver', DefaultRouteResolver::class);
            $resolverClass = DefaultModelRouteResolver::class;

            return new $resolverClass;
        });
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->mergeConfigFrom(__DIR__ . '/../config/analytics.php', 'analytics');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'analytics');

        // php artisan vendor:publish analytics-assets
        $this->publishes([
            __DIR__ . '/../public/dist' => public_path('vendor/prajwal89/analytics'),
        ], 'analytics-assets');

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
            $this->commands([
                SyncGeoLiteDbCommand::class,
                SvgStorageLinkCommand::class,
            ]);
        }
    }
}
