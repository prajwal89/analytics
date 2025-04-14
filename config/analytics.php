<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;
use Prajwal89\Analytics\Services\DefaultModelRouteResolver;

return [
    'use_queue' => false,
    'queue_name' => 'low',
    'record_bias' => true,

    /**
     * for getting county and city data.
     * for syncing with latest database
     * run php artisan analytics:sync-geolite-db-command
     */
    'geolite_db_path' => Storage::path('GeoLite2-City.mmdb'),

    /**
     * Resolve route to its corresponding model
     * You need to implement this
     */
    'route_resolver' => DefaultModelRouteResolver::class,

    /**
     * for bias filament resource
     * add models with the searchable using here this will
     * be used to search biasable1 and biasable2
     */
    'biasable' => [
        // e.g.
        // Tag::class => [
        //     'searchable_using' => 'name'
        // ],
    ],
];
