<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\BorrowController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::apiResource('role', RoleController::class)->middleware(['auth:api', 'isOwner']);
    Route::apiResource('category', CategoryController::class);
    
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    });
    Route::get('/me', [AuthController::class, 'getMe'])->middleware('auth:api');

    Route::apiResource('book', BookController::class);

    Route::post('/profile', [ProfileController::class, 'updateOrStore'])->middleware('auth:api');

    Route::get('/borrow', [BorrowController::class, 'index'])->middleware(['auth:api', 'isOwner']);
    Route::post('/borrow', [BorrowController::class, 'updateOrStore'])->middleware('auth:api');
    Route::delete('/borrow/{borrow}', [BorrowController::class, 'destroy'])->middleware(['auth:api', 'isOwner']);
});