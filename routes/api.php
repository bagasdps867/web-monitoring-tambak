<?php

use App\Http\Controllers\API\ApiSensorController;
use Illuminate\Support\Facades\Route;


Route::post('/sensor', [ApiSensorController::class, 'store']);

use App\Http\Controllers\DashboardController;

Route::post('/sensor-data', [DashboardController::class, 'storeFromDevice']);