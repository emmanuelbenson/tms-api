<?php

use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::get('/', function () {
        return response()->json(['message' => 'TMS API version 1.0'], 200);
    });

    Route::prefix('auth')->group(function () {

    });
});
