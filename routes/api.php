<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Prajwal89\Analytics\Http\Controllers\PageViewController;

Route::middleware([
    'web',
    ...config('analytics.middleware') ? [
        config('analytics.middleware')
    ] : []
])->name('analytics')->prefix('api/an')->group(function (): void {
    Route::post('/', PageViewController::class);
});
