<?php

use App\Http\Middleware\CheckAdminMiddleware;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {


    Route::prefix('auth')->group(apiRoutes(1, 'auth'));
    Route::prefix('moon-api')
        ->middleware(['moonshine'])
        ->group(apiRoutes(1, 'moon-api', true));
    Route::prefix('admin')
        ->middleware(['auth:sanctum', CheckAdminMiddleware::class])
        ->group(apiRoutes(1, 'admin', true));
    apiRoutes(1, 'main', false);
});
