<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SquadraController;

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

Route::get('/', [SquadraController::class, 'index']);
Route::post('/estrai', [SquadraController::class, 'estrai'])->name('estrai');
Route::post('/reset', [SquadraController::class, 'reset'])->name('reset');
