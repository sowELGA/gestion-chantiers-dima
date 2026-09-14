<?php

use App\Http\Controllers\ChantierController;
use App\Http\Controllers\ChefProjet\ChefApproController;
use App\Http\Controllers\ChefProjet\DashboardController;
use App\Http\Controllers\ChefProjet\TacheController;
use App\Http\Controllers\ChefProjet\ValidationController;
use App\Http\Controllers\RapportChantierController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'premiere_connexion', 'role:chef_projet'])
    ->prefix('chef-projet')
    ->name('chef_projet.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'chefProjet'])->name('dashboard');

        // Chantiers
        Route::prefix('chantiers')->name('chantiers.')->group(function () {
            Route::get('/', [ChantierController::class, 'indexChefProjet'])->name('index');
            Route::get('/{chantier}', [ChantierController::class, 'showChefProjet'])->name('show');
        });

        // Gestion du Planning (Phases, Tâches & Gantt)
        Route::prefix('chantiers/{chantier}')->group(function () {
            
            // Phases
            Route::prefix('phases')->name('phases.')->group(function () {
                Route::get('/', [TacheController::class, 'indexPhases'])->name('index');
                Route::get('/create', [TacheController::class, 'createPhase'])->name('create');
                Route::post('/', [TacheController::class, 'storePhase'])->name('store');
                Route::get('/{phase}/edit', [TacheController::class, 'editPhase'])->name('edit');
                Route::patch('/{phase}', [TacheController::class, 'updatePhase'])->name('update');
                Route::delete('/{phase}', [TacheController::class, 'destroyPhase'])->name('destroy');

                // Tâches
                Route::prefix('{phase}/taches')->name('taches.')->group(function () {
                    Route::get('/', [TacheController::class, 'indexTaches'])->name('index');
                    Route::get('/create', [TacheController::class, 'createTache'])->name('create');
                    Route::post('/', [TacheController::class, 'storeTache'])->name('store');
                    Route::get('/{tache}/edit', [TacheController::class, 'editTache'])->name('edit');
                    Route::patch('/{tache}', [TacheController::class, 'updateTache'])->name('update');
                    Route::delete('/{tache}', [TacheController::class, 'destroyTache'])->name('destroy');
                    Route::patch('/{tache}/avancement', [TacheController::class, 'mettreAJourAvancement'])->name('avancement');
                });
            });
        });

        // Validation des Pointages
        Route::prefix('recap{chantier}')->name('recap.')->group(function () {
            Route::get('/', [ValidationController::class, 'validationChefProjet'])->name('validation');
            Route::post('/valider', [ValidationController::class, 'valider'])->name('valider');
            Route::post('/rejeter', [ValidationController::class, 'rejeter'])->name('rejeter');
        });

        // Approvisionnements
        Route::prefix('approvisionnements')->name('appro.')->group(function () {
            Route::get('/', [ChefApproController::class, 'index'])->name('index');
            Route::get('/create', [ChefApproController::class, 'create'])->name('create');
            Route::post('/', [ChefApproController::class, 'store'])->name('store');
            Route::get('/{chantier}/{demande}/edit', [ChefApproController::class, 'edit'])->name('edit');
            Route::put('/{chantier}/{demande}', [ChefApproController::class, 'update'])->name('update');
            Route::delete('/{chantier}/{demande}', [ChefApproController::class, 'destroy'])->name('destroy');
        });

        Route::get('/rapports',[RapportChantierController::class, 'index'])->name('rapports.index');
        Route::get('/rapports/create',[RapportChantierController::class, 'create'])->name('rapports.create');
        Route::post('/rapports',[RapportChantierController::class, 'store'])->name('rapports.store');
        Route::get('/rapports/{rapport}',[RapportChantierController::class, 'show'])->name('rapports.show');
        Route::get('/rapports/{rapport}/edit',[RapportChantierController::class, 'edit'])->name('rapports.edit');
        Route::patch('/rapports/{rapport}',[RapportChantierController::class, 'update'])->name('rapports.update');
        Route::delete('/rapports/{rapport}',[RapportChantierController::class, 'destroy'])->name('rapports.destroy');
    });
