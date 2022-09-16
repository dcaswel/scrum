<?php

use App\Http\Controllers\EstimationController;
use App\Http\Controllers\GuidelinesController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canResetPassword' => Route::has('password.request'),
        'status' => session('status'),
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/estimation', [EstimationController::class, 'index'])->name('estimation');
    Route::post('/choose', [EstimationController::class, 'choose'])->name('choose');
    Route::delete('/reset/{team}', [EstimationController::class, 'reset'])->name('reset');
    Route::patch('/reset-user', [EstimationController::class, 'resetUser'])->name('reset-user');
    Route::get('/runner', [EstimationController::class, 'runner'])->name('runner');

    Route::controller(GuidelinesController::class)->prefix('guidelines')->group(function() {
        Route::get('edit', 'edit')->name('guidelines.edit');
        Route::post('/', 'create')->name('guidelines.create');
        Route::put('{guideline}', 'update')->name('guidelines.update');
    });
});
