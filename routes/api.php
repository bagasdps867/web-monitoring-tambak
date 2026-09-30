<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\API\ApiSensorController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. Rute untuk menyimpan data sensor masuk
Route::post('/sensor', [ApiSensorController::class, 'store']);

// 2. Rute untuk laporan & riwayat sensor (menggunakan getHistoryData)
Route::get('/laporan', [SensorController::class, 'getHistoryData']);

// 3. Rute untuk fitur AI Rekomendasi
Route::post('/ai-rekomendasi', [SensorController::class, 'getAiRekomendasi']);

// 4. Rute untuk mengambil data ambang batas saat aplikasi dibuka
Route::get('/get-ambang-batas', [SensorController::class, 'getAmbangBatas']);

// 5. Rute untuk menyimpan data ambang batas dari slider HP
Route::post('/set-ambang-batas', [SensorController::class, 'setAmbangBatas']);