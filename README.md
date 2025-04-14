# Analytic Module

## Installation

- Register the plugin in your admin panel.  
- Publish the JavaScript file using: `php artisan vendor:publish --tag=analytics-assets`  
- exclude the route from csrf checking        
    ```php
    $middleware->validateCsrfTokens(except: [
         '/api/an'
    ]);
    ```
- Register `PageViewsTrendChart::class` as a dashboard widget (optional).  
- Register `PageViewsDoughnutChart::class` as a dashboard widget (optional).  
- Download the GeoLite database required for location data of the users using:  
  `php artisan analytics:sync-geolite-db-command`  
- Add the following to your scheduler to update the database periodically (ideally every 30 days) (optional but recommended):

```php
Schedule::command('analytics:sync-geolite-db-command')
    ->withoutOverlapping()
    ->twiceMonthly();
```

## Configuration
