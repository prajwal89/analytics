<?php

declare(strict_types=1);

use Prajwal89\Analytics\Services\DefaultModelRouteResolver;

return [
    'use_queue' => false,
    'queue_name' => 'low',
    'record_bias' => true,

    /**
     * for getting county and city data.
     * for syncing with latest database
     * run php artisan analytics:sync-geolite-db
     */
    'geolite_db_path' => storage_path('app/private/GeoLite2-City.mmdb'),

    /**
     * Resolve route to its corresponding model
     * You need to implement this
     */
    'route_resolver' => DefaultModelRouteResolver::class,

    /**
     * Middleware FQN
     * You can use it to exclude the page views from specific users like admin,mod etc
     */
    'middleware' => null,

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
