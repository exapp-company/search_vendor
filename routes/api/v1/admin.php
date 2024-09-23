<?php

use App\Enums\ShopStatus;
use App\Http\Controllers\API\V1\Admin\CityController;
use App\Http\Controllers\API\V1\Admin\DashboardController;
use App\Http\Controllers\API\V1\Admin\FeedController;
use App\Http\Controllers\API\V1\Admin\FeedStatusController;
use App\Http\Controllers\API\V1\Admin\ShopController;
use App\Http\Controllers\API\V1\Admin\UserController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\SynonymController;
use Illuminate\Support\Facades\Route;

Route::prefix('city')->group(function () {
    Route::get('', [CityController::class, 'index']);
    Route::post('', [CityController::class, 'store']);
    Route::get('{city}', [CityController::class, 'show']);
    Route::put('{city}', [CityController::class, 'update']);
    Route::delete('{city}', [CityController::class, 'destroy']);
});

Route::prefix('user')->group(function () {
    Route::get('', [UserController::class, 'index']);
    Route::post('', [UserController::class, 'store']);
    Route::get('{user}', [UserController::class, 'show']);
    Route::put('{user}', [UserController::class, 'update']);
    Route::delete('{user}', [UserController::class, 'destroy']);
});

Route::prefix('shop')->middleware('auth:sanctum')->group(function () {
    Route::get('', [ShopController::class, 'index']);
    Route::post('', [ShopController::class, 'store']);
    Route::get('{shop}', [ShopController::class, 'show']);
    Route::put('{shop}', [ShopController::class, 'update']);
    Route::delete('{shop}', [ShopController::class, 'destroy']);
    Route::post('{shop}/feed', [FeedController::class, 'updateShopFeed']);
    Route::post('upload-logo/{shop}', [ShopController::class, 'uploadLogo']);
});


Route::prefix('feed')->group(function () {
    Route::get('', [FeedController::class, 'index']);
    Route::post('', [FeedController::class, 'store']);
    Route::get('{feed}', [FeedController::class, 'show']);
    Route::put('{feed}', [FeedController::class, 'update']);
    Route::delete('{feed}', [FeedController::class, 'destroy']);
});


Route::prefix('feed-status')->group(function () {
    Route::get('', [FeedStatusController::class, 'index']);
    Route::post('', [FeedStatusController::class, 'store']);
    Route::get('{feedStatus}', [FeedStatusController::class, 'show']);
    Route::put('{feedStatus}', [FeedStatusController::class, 'update']);
    Route::delete('{feedStatus}', [FeedStatusController::class, 'destroy']);
});

Route::prefix('dashboard')->group(function () {
    Route::get('', [DashboardController::class, 'index']);
});

Route::apiResource('locations', LocationController::class);

Route::apiResource('synonyms', SynonymController::class);
Route::post('synonyms/refresh', [SynonymController::class, 'refreshIndex']);
// dd(ShopStatus::cases());
Route::put('{type}/{id}/set-status/{status}', [StatusController::class, 'change'])
    ->whereIn('type', ['shop', 'office'])
    ->whereIn('status', ['active', 'pending', 'rejected', 'inactive']);

Route::apiResource('pages', PageController::class);
