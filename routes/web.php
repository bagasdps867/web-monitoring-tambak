<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SensorController;

Auth::routes();

// --- ROUTE PUBLIC & LANDING ---
Route::redirect('/', '/tentang-tambak')->name('root');
Route::get('/home', fn () => view('home'))->name('home');
Route::get('/tentang-tambak', fn () => view('home'))->name('tentang-tambak');

// --- ROUTE DASHBOARD & AI ---
Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/check-water', [DashboardController::class, 'checkWaterQuality'])->name('check-water');
    Route::get('/ai', [DashboardController::class, 'getAiRecommendation'])->name('ai');
});

// --- ROUTE HISTORY ---
Route::prefix('history')->name('history.')->group(function () {
    Route::get('/', [SensorController::class, 'history'])->name('index');
    Route::get('/data', [SensorController::class, 'getHistoryData'])->name('data');
    Route::get('/filter', [SensorController::class, 'filterHistory'])->name('filter');
});

// --- ROUTE API DATA SENSOR ---
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/sensor-data', [DashboardController::class, 'getSensorData'])->name('sensor-data');
    Route::get('/sensor/latest', [SensorController::class, 'latestApi'])->name('sensor.latest');
});

// --- UTILITY ---
Route::get('/update-kualitas-lama', [DashboardController::class, 'updateMissingQuality'])->name('update.kualitas-lama');