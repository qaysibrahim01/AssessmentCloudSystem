<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChraController;
use App\Http\Controllers\Admin\AdminChraController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminChraDeleteController;
use App\Http\Controllers\Committee\CommitteeChraController;
use App\Http\Controllers\Admin\AdminUploadedChraController;


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
    | DASHBOARDS (ROLE-PROTECTED)
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:assessor')
        ->get('/assessor/dashboard', function () {
            return view('assessor.dashboard');
        })
        ->name('assessor.dashboard');

    Route::middleware('role:committee')
        ->get('/committee/dashboard', function () {
            return view('committee.dashboard');
        })
        ->name('committee.dashboard');


    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/dashboard', [AdminDashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('/chra', [AdminChraController::class, 'index'])
                ->name('chra.index');

            Route::get('/chra/{chra}', [AdminChraController::class, 'show'])
                ->name('chra.show');

            Route::post('/chra/{chra}/approve', [AdminChraController::class, 'approve'])
                ->name('chra.approve');

            Route::post('/chra/{chra}/reject', [AdminChraController::class, 'reject'])
                ->name('chra.reject');

            Route::post('/chra-delete/{deleteRequest}/approve',
                [AdminChraDeleteController::class, 'approve']
            )->name('chra.delete.approve');

            Route::post('/chra-delete/{deleteRequest}/reject',
                [AdminChraDeleteController::class, 'reject']
            )->name('chra.delete.reject');

            Route::get('/chra/uploaded/create',
                [AdminUploadedChraController::class, 'create']
            )->name('chra.uploaded.create');

            Route::post('/chra/uploaded',
                [AdminUploadedChraController::class, 'store']
            )->name('chra.uploaded.store');

            Route::delete('/chra/{chra}/uploaded-delete',
                [AdminUploadedChraController::class, 'destroy']
            )->name('chra.uploaded.destroy');


        });

    /*
    |--------------------------------------------------------------------------
    | ASSESSOR – CHRA MODULE
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:assessor')
        ->prefix('chra')
        ->name('chra.')
        ->group(function () {

            // =========================
            // STATIC ROUTES FIRST
            // =========================
            Route::get('/delete-history',
                [\App\Http\Controllers\ChraDeleteHistoryController::class, 'index']
            )->name('delete.history');

            Route::get('/', [ChraController::class, 'index'])->name('index');
            Route::get('/create', [ChraController::class, 'create'])->name('create');
            Route::post('/', [ChraController::class, 'store'])->name('store');

            // =========================
            // UPLOADED PDF VIEW
            // =========================
            Route::get('/{chra}/uploaded',
                [ChraController::class, 'showUploaded']
            )->middleware('can:view,chra')
            ->name('show.uploaded');

            // =========================
            // MAIN CHRA ROUTES
            // =========================
            Route::get('/{chra}',
                [ChraController::class, 'show']
            )->middleware('can:view,chra')
            ->name('show');

            Route::get('/{chra}/edit',
                [ChraController::class, 'edit']
            )->middleware('can:update,chra')
            ->name('edit');

            Route::post('/{chra}/update-sections',
                [ChraController::class, 'updateSections']
            )->middleware('can:update,chra')
            ->name('update-sections');

            Route::post('/{chra}/save-draft',
                [ChraController::class, 'saveDraft']
            )->middleware('can:update,chra')
            ->name('save-draft');

            Route::post('/{chra}/submit',
                [ChraController::class, 'submitForApproval']
            )->middleware('can:update,chra')
            ->name('submit');

            Route::post('/{chra}/request-delete',
                [ChraController::class, 'requestDelete']
            )->middleware('can:requestDelete,chra')
            ->name('request-delete');

            Route::get('/{chra}/pdf',
                [ChraController::class, 'downloadPdf']
            )->middleware('can:view,chra')
            ->name('download');

            // =========================
            // CHRA SUB-RESOURCES
            // =========================
            Route::post('/{chra}/work-units',
                [\App\Http\Controllers\ChraWorkUnitController::class, 'store']
            )->name('workunit');

            Route::delete('/work-units/{unit}',
                [\App\Http\Controllers\ChraWorkUnitController::class, 'destroy']
            )->name('workunit.delete');

            Route::post('/{chra}/chemicals',
                [\App\Http\Controllers\ChraChemicalController::class, 'store']
            )->name('chemical');

            Route::delete('/chemicals/{chemical}',
                [\App\Http\Controllers\ChraChemicalController::class, 'destroy']
            )->name('chemical.delete');

            Route::post('/{chra}/exposures',
                [\App\Http\Controllers\ChraExposureController::class, 'store']
            )->name('exposure.store');

            Route::post('/{chra}/recommendations',
                [\App\Http\Controllers\ChraRecommendationController::class, 'store']
            )->name('recommendation');

            Route::delete('/recommendations/{rec}',
                [\App\Http\Controllers\ChraRecommendationController::class, 'destroy']
            )->name('recommendation.delete');

            Route::post('/{chra}/autosave',
                [\App\Http\Controllers\ChraAutosaveController::class, 'store']
            )->name('autosave');

        });


    /*
    |--------------------------------------------------------------------------
    | COMMITTEE – READ ONLY
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:committee')
        ->prefix('committee')
        ->name('committee.')
        ->group(function () {

            Route::get('/dashboard', function () {
                return view('committee.dashboard');
            })->name('dashboard');

            Route::get('/chra',
                [\App\Http\Controllers\Committee\CommitteeChraController::class, 'index']
            )->name('chra.index');

            // 🔹 Uploaded PDF view (MUST be before {chra})
            Route::get('/chra/{chra}/uploaded',
                [\App\Http\Controllers\Committee\CommitteeChraController::class, 'showUploaded']
            )->middleware('can:view,chra')
            ->name('chra.show.uploaded');

            Route::get('/chra/{chra}',
                [\App\Http\Controllers\Committee\CommitteeChraController::class, 'show']
            )->middleware('can:view,chra')
            ->name('chra.show');

            Route::get('/chra/{chra}/pdf',
                [\App\Http\Controllers\Committee\CommitteeChraController::class, 'downloadPdf']
            )->middleware('can:view,chra')
            ->name('chra.pdf');
        });


});
