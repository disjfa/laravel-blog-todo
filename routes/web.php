<?php

use App\Http\Controllers\ApiDocsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/docs', [ApiDocsController::class, 'ui'])->name('api.docs.ui');
Route::get('/api/docs.json', [ApiDocsController::class, 'json'])->name('api.docs.json');
