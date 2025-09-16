<?php

use App\Http\Middleware\LogRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware([LogRequests::class])->group(function () {
    Route::get('/appTopCategory', [App\Http\Controllers\AppTopController::class, 'index']);
});
