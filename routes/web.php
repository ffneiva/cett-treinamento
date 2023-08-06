<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResearcherController;
use App\Http\Controllers\WorkController;

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

Route::group([
    'middleware' => ['auth', 'verified'],
    'prefix' => '/'
], function () {
    Route::get('/', [HomeController::class, 'index'])->name('index');

    Route::group([
        'prefix' => '/profile'
    ], function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::group([
        'prefix' => '/institution'
    ], function () {
        Route::get('', [InstitutionController::class, 'index'])->name('institution.index');
        Route::post('', [InstitutionController::class, 'store'])->name('institution.store');
        Route::put('/{id}', [InstitutionController::class, 'update'])->name('institution.update');
        Route::delete('/{id}', [InstitutionController::class, 'destroy'])->name('institution.delete');
    });

    Route::group([
        'prefix' => '/researchers'
    ], function () {
        Route::get('/', [ResearcherController::class, 'index'])->name('researcher.index');
        Route::post('', [ResearcherController::class, 'store'])->name('researcher.store');
        Route::put('/{id}', [ResearcherController::class, 'update'])->name('researcher.update');
        Route::delete('/{id}', [ResearcherController::class, 'destroy'])->name('researcher.delete');
    });

    Route::group([
        'prefix' => '/works'
    ], function () {
        Route::get('/', [WorkController::class, 'index'])->name('work.index');
        Route::post('', [WorkController::class, 'store'])->name('work.store');
        Route::post('/{id}', [WorkController::class, 'update'])->name('work.update');
        Route::delete('/{id}', [WorkController::class, 'destroy'])->name('work.delete');
    });
});

require __DIR__.'/auth.php';
