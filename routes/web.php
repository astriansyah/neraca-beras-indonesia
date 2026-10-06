<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web routes — publik & read-only (hanya GET, tanpa autentikasi)
|--------------------------------------------------------------------------
*/

Route::controller(DashboardController::class)->group(function () {
    Route::get('/', 'index')->name('dashboard');
    Route::get('/produksi', 'production')->name('production');
    Route::get('/konsumsi', 'consumption')->name('consumption');
    Route::get('/perdagangan', 'trade')->name('trade');
    Route::get('/stok', 'stock')->name('stock');
    Route::get('/neraca', 'balance')->name('balance');
    Route::get('/simulasi', 'simulation')->name('simulation');
    Route::get('/data', 'data')->name('data');
    Route::get('/metodologi', 'methodology')->name('methodology');
});
