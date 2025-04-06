# Analytic Module

## Installation

- Register plugin in your admin panel
- Publish js file `php artisan vendor:publish analytics-assets`
- You can register `PageViewsTrendChart::class` in dashboard widgets,
- You can register `PageViewsDoughnutChart::class` in dashboard widgets,
- Download Geolite Database of location details with `php artisan analytics:sync-geolite-db-command`
- Include this in your scheduler for updating the database periodically (every 15 days ideally).
  
  ```php
      Schedule::command('analytics:sync-geolite-db-command')
        ->withoutOverlapping()
        ->twiceMonthly();
  ```

## Configuration
