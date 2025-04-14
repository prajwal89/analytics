<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Prajwal89\Analytics\Http\Controllers\PageViewController;

Route::middleware(['web'])->name('analytics')->prefix('api')->group(function (): void {
    Route::post('/', [PageViewController::class, 'store']);
});
