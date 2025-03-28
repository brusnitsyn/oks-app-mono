<?php

use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportTemplateController;
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

//    Route::prefix('patient')->group(function () {
//        Route::prefix('{patient}')->group(function () {
//            Route::get('/', [\App\Http\Controllers\PatientController::class, 'show'])->name('patients.show');
//            Route::put('/', [\App\Http\Controllers\PatientController::class, 'update'])->name('patients.update');
//        });
//    });

    Route::prefix('patient')->group(function () {
        Route::prefix('{patient}')->group(function () {
            Route::get('/', [\App\Http\Controllers\PatientController::class, 'show'])->name('patients.show');
            Route::put('/', [\App\Http\Controllers\PatientController::class, 'update'])->name('patients.update');
            Route::delete('/', [\App\Http\Controllers\PatientController::class, 'delete'])->name('patients.delete');
            Route::put('/restore', [\App\Http\Controllers\PatientController::class, 'restore'])->name('patients.restore');
        });
    });

    Route::prefix('control-call')->group(function () {
        Route::prefix('{controlCall}')->group(function () {
            Route::put('/', [\App\Http\Controllers\MedCardControlCallController::class, 'update'])->name('control.call.update');
        });
    });

    Route::prefix('reports')->group(function () {
        // Страницы отчетов
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/{template}', [ReportController::class, 'show'])->name('reports.show');

        // API для выполнения отчетов
        Route::post('/{template}/execute', [ReportController::class, 'execute'])
            ->name('reports.execute');
    });

    Route::prefix('api')->group(function () {
//        Route::post('report-templates', [ReportTemplateController::class, 'store'])
//            ->name('api.report-templates.store');
        // Шаблоны отчетов
        Route::apiResource('report-templates', ReportTemplateController::class)
            ->except(['update'])->name('store', 'api.report-templates.store');

        // Выполнение отчетов
        Route::post('reports/{template}/execute', [ReportController::class, 'execute']);

        Route::post('/report-templates/get-options', [ReportTemplateController::class, 'getOptions'])
            ->name('report-templates.get-options');

        // Справочные данные для параметров
        Route::get('reference-data/{table}', function ($table) {
            $allowedTables = ['lpus', 'mkbs', 'control_call_results', 'surveys'];

            if (!in_array($table, $allowedTables)) {
                abort(404);
            }

            return DB::table($table)->get();
        });
    });
//
//    Route::prefix('api')->group(function () {
//        Route::prefix('reports')->group(function () {
//            Route::get('structure', [ReportController::class, 'index']);
//            Route::post('table-structure', [ReportController::class, 'getTableStructure']);
//            Route::post('generate', [ReportController::class, 'generateReport']);
//            Route::post('available-tables', [ReportController::class, 'getAvailableTables']);
//            Route::post('export', [ReportController::class, 'exportReport']);
//        });
//    });
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
