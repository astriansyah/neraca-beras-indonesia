<?php

use App\Http\Controllers\Api\StatsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Publik, Read-Only, Tanpa Autentikasi
|--------------------------------------------------------------------------
*/

Route::prefix('stats')->group(function () {
    Route::get('/summary', [StatsController::class, 'summary'])->name('api.stats.summary');
    Route::get('/production', [StatsController::class, 'production'])->name('api.stats.production');
    Route::get('/consumption', [StatsController::class, 'consumption'])->name('api.stats.consumption');
    Route::get('/trade', [StatsController::class, 'trade'])->name('api.stats.trade');
    Route::get('/stock', [StatsController::class, 'stock'])->name('api.stats.stock');
    Route::get('/balance', [StatsController::class, 'balance'])->name('api.stats.balance');
    Route::get('/simulation', [StatsController::class, 'simulation'])->name('api.stats.simulation');
});

Route::get('/data-points', [StatsController::class, 'dataPoints'])->name('api.data-points');
Route::get('/sources', [StatsController::class, 'sources'])->name('api.sources');
