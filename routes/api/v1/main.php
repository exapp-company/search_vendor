<?php

use App\Http\Controllers\API\V1\FavoriteProductController;
use App\Http\Controllers\API\V1\FeedController;
use App\Http\Controllers\API\V1\LocalizationController;
use App\Http\Controllers\API\V1\MainController;
use App\Http\Controllers\API\V1\ProfileController;
use App\Http\Controllers\API\V1\SearchController;
use App\Http\Controllers\API\V1\ShopController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DialogController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductListController;
use App\Http\Controllers\TestController;
use App\Http\Middleware\EnsureIsUserOwnerMiddleware;
use Illuminate\Support\Facades\Route;


Route::get('test', TestController::class);

Route::post('set-locale', LocalizationController::class);


Route::get('initialize', [MainController::class, 'init']);
Route::any('geolocation', [MainController::class, 'geolocation']);

Route::get('', [MainController::class, 'index']);
Route::get('search', [SearchController::class, 'search']);
Route::get('quick-search', [SearchController::class, 'quickSearch']);

Route::prefix('profile')->middleware('auth:sanctum')->group(function () {
    Route::get('', [ProfileController::class, 'index']);
    Route::put('', [ProfileController::class, 'update']);
    Route::put('change-password', [ProfileController::class, 'changePassword']);
    Route::post('create-business', [ProfileController::class, 'createBusiness']);
});


Route::prefix('favorite-products')->middleware('auth:sanctum')->group(function () {
    Route::get('', [FavoriteProductController::class, 'index']);
    Route::post('/add', [FavoriteProductController::class, 'addSome']);
    Route::delete('/remove', [FavoriteProductController::class, 'removeSome']);
    Route::post('{product}/add', [FavoriteProductController::class, 'add']);
    Route::delete('{product}/remove', [FavoriteProductController::class, 'remove']);
    Route::delete('clear', [FavoriteProductController::class, 'clear']);
});
Route::post('favorite-products/share', [ProductListController::class, 'store']);
Route::get('favorite-products/share/{product_list}', [ProductListController::class, 'index']);


Route::prefix('shop')->middleware(['auth:sanctum', EnsureIsUserOwnerMiddleware::class])->group(function () {
    Route::get('', [ShopController::class, 'index']);
    Route::get('{shop}', [ShopController::class, 'show']);
    Route::post('', [ShopController::class, 'store']);
    Route::put('{shop}', [ShopController::class, 'update']);
    Route::delete('{shop}', [ShopController::class, 'destroy']);
    Route::post('{shop}/feed', [FeedController::class, 'updateShopFeed']);
    Route::post('upload-logo/{shop}', [ShopController::class, 'uploadLogo']);
    Route::apiResource('{shop}/office', OfficeController::class);
    Route::post('{shop}/import', [ShopController::class, 'startImport'])->name("import-shop");
    Route::post('{shop}/reindex', [ShopController::class, 'refreshIndex'])->name("reindex-shop");
});


Route::apiResource('offices', OfficeController::class)->middleware(['auth:sanctum', EnsureIsUserOwnerMiddleware::class]);

Route::prefix('feed')->middleware('auth:sanctum')->group(function () {
    Route::get('{feed}', [FeedController::class, 'show']);
    Route::post('', [FeedController::class, 'store']);
    Route::put('{feed}', [FeedController::class, 'update']);
    Route::delete('{feed}', [FeedController::class, 'destroy']);
});

Route::prefix('products')->group(function () {
    Route::get('', [ProductController::class, 'index']);
    Route::get('{product}', [ProductController::class, 'show']);

    Route::middleware(['auth:sanctum', EnsureIsUserOwnerMiddleware::class])->group(function () {
        Route::post('', [ProductController::class, 'store']);
        Route::put('{product}', [ProductController::class, 'update']);
        Route::delete('{product}', [ProductController::class, 'destroy']);
        Route::get('shop/{id}', [ProductController::class, 'productsForShop']);
    });

    //Route::get('s/{product:slug}', [ProductController::class, 'show']);
});

Route::get('/cities/{city}/locations', [LocationController::class, 'getLocationByCity']);
Route::get('/locations/{location}/children', [LocationController::class, 'getChildren']);

Route::get("q/{page:slug}", [PageController::class, "getProducts"]);

Route::post("/files", [FileController::class, 'store'])->middleware('auth:sanctum');
Route::delete("/files/{file}", [FileController::class, 'destroy'])->middleware('auth:sanctum');


//Route::middleware(['auth:sanctum', EnsureIsUserOwnerMiddleware::class])->group(function () {

Route::prefix('chat')->group(function () {
    Route::get('', [ChatController::class, 'index']);
    Route::put('{shop}', [ChatController::class, 'activateToggle']);

    Route::prefix('dialogs')->group(function () {
        Route::get('', [DialogController::class, 'index']);
        Route::post('', [DialogController::class, 'store']);

        Route::prefix('/messages')->group(function () {
            Route::get('{dialog}', [MessageController::class, 'index']);
            Route::post('{dialog}', [MessageController::class, 'store']);
            Route::post('{dialog}/upload', [MessageController::class, 'upload']);
            Route::post('{message}/read', [MessageController::class, 'read']);
            Route::put('{message}', [MessageController::class, 'update']);
            Route::delete('{message}', [MessageController::class, 'destroy']);
        });
    });
});

Route::get('chat', TestController::class);



//});
