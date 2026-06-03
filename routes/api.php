<?php

use App\Http\Controllers\Api\V1\BlogController;
use App\Http\Controllers\Api\V1\CustomerListController;
use App\Http\Controllers\Api\V1\MeController;
use App\Http\Controllers\Api\V1\TodoController;
use App\Http\Controllers\Api\V1\TodoMoveController;
use App\Models\Blog;
use App\Models\Customer;
use App\Models\Todo;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::prefix('customers/{customer}')->group(function () {
        Route::apiResource('blogs', BlogController::class);
        Route::apiResource('todos', TodoController::class);
        Route::patch('todos/{todo}/move', TodoMoveController::class)->name('todos.move');
    });

    Route::get('me', MeController::class);
    Route::get('customers', CustomerListController::class);
});

Route::model('customer', Customer::class);
Route::model('blog', Blog::class);
Route::model('todo', Todo::class);
