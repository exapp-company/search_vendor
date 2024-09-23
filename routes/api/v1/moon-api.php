<?php

use App\Http\Controllers\API\V1\FeedController;
use Illuminate\Support\Facades\Route;

Route::patch("{type}/update-feed/{id}", [FeedController::class, 'updateShopFeed'])->name("feed-update");
