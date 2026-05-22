<?php

use App\Http\Controllers\Api\V1\BlogController;
use App\Http\Controllers\Api\V1\TodoController;
use App\Http\Controllers\Api\V1\TodoMoveController;
use App\Models\Blog;
use App\Models\Customer;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::prefix('customers/{customer}')->group(function () {
        Route::apiResource('blogs', BlogController::class);
        Route::apiResource('todos', TodoController::class);
        Route::patch('todos/{todo}/move', TodoMoveController::class)->name('todos.move');
    });

    Route::get('me', function (Request $request) {
        return $request->user()->load('customers');
    });

    Route::get('customers', function (Request $request) {
        return $request->user()->customers;
    });
});

Route::model('customer', Customer::class);
Route::model('blog', Blog::class);
Route::model('todo', Todo::class);
