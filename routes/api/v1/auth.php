<?php


use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\API\V1\Auth\RegisterController;
use App\Http\Controllers\API\V1\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [RegisterController::class, 'store']);
    Route::post('forgot-password', ForgotPasswordController::class);
    Route::post('reset-password/{token}', ResetPasswordController::class)
        ->name('password.reset');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::post('refresh', [AuthController::class, 'refresh']);
