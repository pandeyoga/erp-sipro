<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'success' => false,
        'message' => 'unauthorized'
    ]);
})->name('login');