<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChraController;
use App\Http\Controllers\HirarcController;
use App\Http\Controllers\NraController;
use App\Http\Controllers\Admin\AdminChraController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminChraDeleteController;
use App\Http\Controllers\Committee\CommitteeChraController;
use App\Http\Controllers\Admin\AdminUploadedChraController;
use App\Http\Controllers\Admin\AdminUploadedHirarcController;
use App\Http\Controllers\Admin\AdminUploadedNraController;
use App\Http\Controllers\Admin\AdminUserApprovalController;


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
    'approved',
])->group(function () {

    // Shared CHRA PDF download (admins/committee/assessor who can view)
    Route::get('/chra/{chra}/pdf',
        [ChraController::class, 'downloadPdf']
    )->middleware('can:view,chra')
    ->name('chra.download');

    // Shared HIRARC/NRA PDF
    Route::get('/hirarc/{hirarc}/pdf',
        [HirarcController::class, 'downloadPdf']
    )->middleware('can:view,hirarc')
    ->name('hirarc.pdf');

    Route::get('/nra/{nra}/pdf',
        [NraController::class, 'downloadPdf']
    )->middleware('can:view,nra')
    ->name('nra.pdf');

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

            Route::get('/hirarc/uploaded/create',
                [AdminUploadedHirarcController::class, 'create']
            )->name('hirarc.uploaded.create');

            Route::post('/hirarc/uploaded',
                [AdminUploadedHirarcController::class, 'store']
            )->name('hirarc.uploaded.store');

            Route::delete('/hirarc/{hirarc}/uploaded-delete',
                [AdminUploadedHirarcController::class, 'destroy']
            )->name('hirarc.uploaded.destroy');

            Route::get('/nra/uploaded/create',
                [AdminUploadedNraController::class, 'create']
            )->name('nra.uploaded.create');

            Route::post('/nra/uploaded',
                [AdminUploadedNraController::class, 'store']
            )->name('nra.uploaded.store');

            Route::delete('/nra/{nra}/uploaded-delete',
                [AdminUploadedNraController::class, 'destroy']
            )->name('nra.uploaded.destroy');

            Route::get('/users', [AdminUserApprovalController::class, 'index'])
                ->name('users.index');
            Route::post('/users/{user}/approve', [AdminUserApprovalController::class, 'approve'])
                ->name('users.approve');
            Route::post('/users/{user}/reject', [AdminUserApprovalController::class, 'reject'])
                ->name('users.reject');
            Route::post('/users/admin-create', [AdminUserApprovalController::class, 'storeAdmin'])
                ->name('users.create-admin');

            // Admin review for HIRARC
            Route::get('/hirarc', [\App\Http\Controllers\Admin\AdminHirarcController::class, 'index'])
                ->name('hirarc.index');
            Route::get('/hirarc/{hirarc}', [\App\Http\Controllers\Admin\AdminHirarcController::class, 'show'])
                ->name('hirarc.show');
            Route::post('/hirarc/{hirarc}/approve', [\App\Http\Controllers\Admin\AdminHirarcController::class, 'approve'])
                ->name('hirarc.approve');
            Route::post('/hirarc/{hirarc}/reject', [\App\Http\Controllers\Admin\AdminHirarcController::class, 'reject'])
                ->name('hirarc.reject');

            // Admin review for NRA
            Route::get('/nra', [\App\Http\Controllers\Admin\AdminNraController::class, 'index'])
                ->name('nra.index');
            Route::get('/nra/{nra}', [\App\Http\Controllers\Admin\AdminNraController::class, 'show'])
                ->name('nra.show');
            Route::post('/nra/{nra}/approve', [\App\Http\Controllers\Admin\AdminNraController::class, 'approve'])
                ->name('nra.approve');
            Route::post('/nra/{nra}/reject', [\App\Http\Controllers\Admin\AdminNraController::class, 'reject'])
                ->name('nra.reject');
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
    | ASSESSOR - HIRARC MODULE (MVP)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:assessor')
        ->prefix('hirarc')
        ->name('hirarc.')
        ->group(function () {
            Route::get('/', [HirarcController::class, 'index'])->name('index');
            Route::get('/create', [HirarcController::class, 'create'])->name('create');
            Route::post('/', [HirarcController::class, 'store'])->name('store');

            // Uploaded PDF view (MUST be before {hirarc})
            Route::get('/{hirarc}/uploaded',
                [HirarcController::class, 'showUploaded']
            )->middleware('can:view,hirarc')
            ->name('show.uploaded');

            // Main HIRARC routes
            Route::get('/{hirarc}', [HirarcController::class, 'show'])
                ->middleware('can:view,hirarc')
                ->name('show');

            Route::get('/{hirarc}/edit', [HirarcController::class, 'edit'])
                ->middleware('can:update,hirarc')
                ->name('edit');

            Route::post('/{hirarc}/update-sections', [HirarcController::class, 'updateSections'])
                ->middleware('can:update,hirarc')
                ->name('update-sections');

            Route::match(['post','put'],'/{hirarc}/save-draft', [HirarcController::class, 'saveDraft'])
                ->name('save-draft')->middleware('can:update,hirarc');

            Route::match(['post','put'],'/{hirarc}/submit', [HirarcController::class, 'submitForApproval'])
                ->name('submit')->middleware('can:update,hirarc');

            Route::post('/{hirarc}/request-delete', [HirarcController::class, 'requestDelete'])
                ->middleware('can:requestDelete,hirarc')
                ->name('request-delete');

            // HIRARC sub-resources
            Route::post('/{hirarc}/work-units',
                [\App\Http\Controllers\HirarcWorkUnitController::class, 'store']
            )->name('workunit');

            Route::delete('/work-units/{unit}',
                [\App\Http\Controllers\HirarcWorkUnitController::class, 'destroy']
            )->name('workunit.delete');

            Route::post('/{hirarc}/chemicals',
                [\App\Http\Controllers\HirarcChemicalController::class, 'store']
            )->name('chemical');

            Route::delete('/chemicals/{chemical}',
                [\App\Http\Controllers\HirarcChemicalController::class, 'destroy']
            )->name('chemical.delete');

            Route::post('/{hirarc}/exposures',
                [\App\Http\Controllers\HirarcExposureController::class, 'store']
            )->name('exposure.store');

            Route::post('/{hirarc}/recommendations',
                [\App\Http\Controllers\HirarcRecommendationController::class, 'store']
            )->name('recommendation');

            Route::delete('/recommendations/{rec}',
                [\App\Http\Controllers\HirarcRecommendationController::class, 'destroy']
            )->name('recommendation.delete');

            Route::post('/{hirarc}/autosave',
                [\App\Http\Controllers\HirarcAutosaveController::class, 'store']
            )->name('autosave');
        });

    /*
    |--------------------------------------------------------------------------
    | ASSESSOR - NRA MODULE (MVP)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:assessor')
        ->prefix('nra')
        ->name('nra.')
        ->group(function () {
            Route::get('/', [NraController::class, 'index'])->name('index');
            Route::get('/create', [NraController::class, 'create'])->name('create');
            Route::post('/', [NraController::class, 'store'])->name('store');

            // Uploaded PDF view (MUST be before {nra})
            Route::get('/{nra}/uploaded',
                [NraController::class, 'showUploaded']
            )->middleware('can:view,nra')
            ->name('show.uploaded');

            // Main NRA routes
            Route::get('/{nra}', [NraController::class, 'show'])
                ->middleware('can:view,nra')
                ->name('show');

            Route::get('/{nra}/edit', [NraController::class, 'edit'])
                ->middleware('can:update,nra')
                ->name('edit');

            Route::post('/{nra}/update-sections', [NraController::class, 'updateSections'])
                ->middleware('can:update,nra')
                ->name('update-sections');

            Route::match(['post','put'],'/{nra}/save-draft', [NraController::class, 'saveDraft'])
                ->name('save-draft')->middleware('can:update,nra');

            Route::match(['post','put'],'/{nra}/submit', [NraController::class, 'submitForApproval'])
                ->name('submit')->middleware('can:update,nra');

            Route::post('/{nra}/request-delete', [NraController::class, 'requestDelete'])
                ->middleware('can:requestDelete,nra')
                ->name('request-delete');

            // NRA sub-resources
            Route::post('/{nra}/work-units',
                [\App\Http\Controllers\NraWorkUnitController::class, 'store']
            )->name('workunit');

            Route::delete('/work-units/{unit}',
                [\App\Http\Controllers\NraWorkUnitController::class, 'destroy']
            )->name('workunit.delete');

            Route::post('/{nra}/chemicals',
                [\App\Http\Controllers\NraChemicalController::class, 'store']
            )->name('chemical');

            Route::delete('/chemicals/{chemical}',
                [\App\Http\Controllers\NraChemicalController::class, 'destroy']
            )->name('chemical.delete');

            Route::post('/{nra}/exposures',
                [\App\Http\Controllers\NraExposureController::class, 'store']
            )->name('exposure.store');

            Route::post('/{nra}/recommendations',
                [\App\Http\Controllers\NraRecommendationController::class, 'store']
            )->name('recommendation');

            Route::delete('/recommendations/{rec}',
                [\App\Http\Controllers\NraRecommendationController::class, 'destroy']
            )->name('recommendation.delete');

            Route::post('/{nra}/autosave',
                [\App\Http\Controllers\NraAutosaveController::class, 'store']
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

            Route::get('/hirarc',
                [\App\Http\Controllers\Committee\CommitteeHirarcController::class, 'index']
            )->name('hirarc.index');
            Route::get('/hirarc/{hirarc}',
                [\App\Http\Controllers\Committee\CommitteeHirarcController::class, 'show']
            )->middleware('can:view,hirarc')
            ->name('hirarc.show');

            Route::get('/hirarc/{hirarc}/uploaded',
                [\App\Http\Controllers\Committee\CommitteeHirarcController::class, 'showUploaded']
            )->middleware('can:view,hirarc')
            ->name('hirarc.show.uploaded');

            Route::get('/hirarc/{hirarc}/pdf',
                [\App\Http\Controllers\Committee\CommitteeHirarcController::class, 'downloadPdf']
            )->middleware('can:view,hirarc')
            ->name('hirarc.pdf');

            Route::get('/nra',
                [\App\Http\Controllers\Committee\CommitteeNraController::class, 'index']
            )->name('nra.index');
            Route::get('/nra/{nra}',
                [\App\Http\Controllers\Committee\CommitteeNraController::class, 'show']
            )->middleware('can:view,nra')
            ->name('nra.show');

            Route::get('/nra/{nra}/uploaded',
                [\App\Http\Controllers\Committee\CommitteeNraController::class, 'showUploaded']
            )->middleware('can:view,nra')
            ->name('nra.show.uploaded');

            Route::get('/nra/{nra}/pdf',
                [\App\Http\Controllers\Committee\CommitteeNraController::class, 'downloadPdf']
            )->middleware('can:view,nra')
            ->name('nra.pdf');
        });


});
