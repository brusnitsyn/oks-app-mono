<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/', [\App\Http\Controllers\PatientController::class, 'index'])->name('patients.index');
    Route::post('/', [\App\Http\Controllers\PatientController::class, 'create'])->name('patients.create');

    Route::prefix('patient')->group(function () {
        Route::prefix('{patient}')->group(function () {
            Route::get('/', [\App\Http\Controllers\PatientController::class, 'show'])->name('patients.show');
            Route::put('/', [\App\Http\Controllers\PatientController::class, 'update'])->name('patients.update');
        });
    });

    Route::prefix('patient')->group(function () {
        Route::prefix('{patient}')->group(function () {
            Route::get('/', [\App\Http\Controllers\PatientController::class, 'show'])->name('patients.show');
            Route::put('/', [\App\Http\Controllers\PatientController::class, 'update'])->name('patients.update');
            Route::delete('/', [\App\Http\Controllers\PatientController::class, 'delete'])->name('patients.delete');
        });
    });

    Route::prefix('control-call')->group(function () {
        Route::prefix('{controlCall}')->group(function () {
            Route::put('/', [\App\Http\Controllers\MedCardControlCallController::class, 'update'])->name('control.call.update');
        });
    });
});



//Route::middleware([
//    'auth:sanctum',
//    config('jetstream.auth_session'),
//    'verified',
//])->group(function () {
//    Route::get('/patients', function () {
//        return Inertia::render('Dashboard');
//    })->name('dashboard');
//});
