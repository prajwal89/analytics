<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface RouteModelResolverInterface
{
    /**
     * Resolve routes and return them as an array.
     */
    public function resolve(string $url): ?Model;
}
