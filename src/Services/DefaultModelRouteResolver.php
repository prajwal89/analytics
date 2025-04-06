<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Prajwal89\Analytics\Interfaces\RouteModelResolverInterface;

class DefaultModelRouteResolver implements RouteModelResolverInterface
{
    /**
     * Resolve associated the model instance from the route name
     * This should be manually implemented by the user
     */
    public function resolve(string $url): ?Model
    {
        $path = parse_url($url, PHP_URL_PATH);

        if ($path === null) {
            $url .= '/';
            $path = parse_url($url, PHP_URL_PATH);
        }

        $route = Route::getRoutes()->match(request()->create($path));

        return match ($route->getName()) {
            // e.g
            // 'tags.show' => Tag::query()
            //     ->where('slug', $route->parameters()['tag'])
            //     ->first(),
            default => null,
        };
    }
}
