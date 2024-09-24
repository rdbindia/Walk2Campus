<?php

use App\Http\Controllers\WeatherAppController;
use Illuminate\Support\Facades\Route;

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/weather/{postal_code}', [WeatherAppController::class, 'displayWeatherData']);
