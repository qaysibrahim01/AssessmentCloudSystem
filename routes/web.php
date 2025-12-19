<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ChraController;
use App\Http\Controllers\Admin\AdminChraController;
use App\Http\Controllers\Admin\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome-role');
})->name('welcome.role');

/*
|--------------------------------------------------------------------------
| Authenticated
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ROLE REDIRECT
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', function () {
        return match (auth()->user()->role) {
            'admin'     => redirect()->route('admin.dashboard'),
            'committee' => redirect()->route('committee.dashboard'),
            default     => redirect()->route('assessor.dashboard'),
        };
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | DASHBOARDS
    |--------------------------------------------------------------------------
    */
    Route::view('/assessor/dashboard', 'assessor.dashboard')->name('assessor.dashboard');
    Route::view('/committee/dashboard', 'committee.dashboard')->name('committee.dashboard');

    /*
    |--------------------------------------------------------------------------
    | ADMIN – CHRA
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {

        Route::get('/chra', [AdminChraController::class, 'index'])
            ->name('chra.index');

        Route::get('/chra/{chra}', [AdminChraController::class, 'show'])
            ->name('chra.show');

        Route::post('/chra/{chra}/approve', [AdminChraController::class, 'approve'])
            ->name('chra.approve');

        Route::post('/chra/{chra}/reject', [AdminChraController::class, 'reject'])
            ->name('chra.reject');

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');
    });

    /*
    |--------------------------------------------------------------------------
    | CHRA MODULE (ASSESSOR)
    |--------------------------------------------------------------------------
    */
    Route::prefix('chra')->name('chra.')->group(function () {

        Route::get('/', [ChraController::class, 'index'])->name('index');
        Route::get('/create', [ChraController::class, 'create'])->name('create');
        Route::post('/', [ChraController::class, 'store'])->name('store');

        Route::get('/{chra}/edit', [ChraController::class, 'edit'])->name('edit');
        Route::get('/{chra}', [ChraController::class, 'show'])->name('show');

        Route::post('/{chra}/update-sections',
            [ChraController::class, 'updateSections']
        )->name('update-sections');

        /* SECTION C – WORK UNITS */
        Route::post('/{chra}/work-unit',
            [ChraController::class, 'addWorkUnit']
        )->name('workunit');

        Route::delete('/work-unit/{unit}',
            [ChraController::class, 'deleteWorkUnit']
        )->name('workunit.delete');

        /* SECTION D – CHEMICALS */
        Route::post('/{chra}/chemical',
            [ChraController::class, 'addChemical']
        )->name('chemical');

        Route::delete('/chemical/{chemical}',
            [ChraController::class, 'deleteChemical']
        )->name('chemical.delete');

        /* SECTION E – EXPOSURE & RISK */
        Route::post('/{chra}/exposure',
            [ChraController::class, 'storeExposure']
        )->name('exposure.store');

        Route::post('/exposure/{exposure}/risk',
            [ChraController::class, 'storeRiskEvaluation']
        )->name('risk.store');

        /* SECTION F – RECOMMENDATIONS */
        Route::post('/{chra}/recommendation',
            [ChraController::class, 'addRecommendation']
        )->name('recommendation');

        Route::delete('/recommendation/{recommendation}',
            [ChraController::class, 'deleteRecommendation']
        )->name('recommendation.delete');

        /* GLOBAL ACTIONS */
        Route::post('/{chra}/save-draft',
            [ChraController::class, 'saveDraft']
        )->name('save-draft');

        Route::post('/{chra}/submit',
            [ChraController::class, 'submitForApproval']
        )->name('submit');

        /* DELETE REQUEST (FIX FOR YOUR ERROR) */
        Route::post('/{chra}/request-delete',
            [ChraController::class, 'requestDelete']
        )->name('request-delete');

        Route::get('/{chra}/pdf',
            [ChraController::class, 'downloadPdf']
        )->name('download');

        Route::post('{chra}/autosave', [ChraController::class, 'autoSave']
        )->name('autosave');

    });
    
});
