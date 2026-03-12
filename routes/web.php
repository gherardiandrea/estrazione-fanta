<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamDrawController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/', [TeamDrawController::class, 'index']);
Route::post('/setup', [TeamDrawController::class, 'setup'])->name('setup');
Route::post('/draw', [TeamDrawController::class, 'draw'])->name('draw');
Route::post('/reset', [TeamDrawController::class, 'reset'])->name('reset');
Route::post('/clear-history', [TeamDrawController::class, 'clearHistory'])->name('clear-history');
Route::post('/new-configuration', [TeamDrawController::class, 'newConfiguration'])->name('new-configuration');
