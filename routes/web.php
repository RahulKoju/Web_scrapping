<?php

use App\Http\Controllers\ScrapController;
use App\Http\Controllers\ScrapController_V2;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/scrap', [ScrapController::class, 'scrap']);
Route::get('/scrape/v2', [ScrapController_V2::class, 'scrape']);
