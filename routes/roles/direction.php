<?php

use App\Http\Controllers\ChantierController;
use App\Http\Controllers\Direction\DashboardController;
use App\Http\Controllers\Direction\DepenseChantierController;
use App\Http\Controllers\Direction\DirectionApproController;
use App\Http\Controllers\Direction\PersonnelController;
use App\Http\Controllers\Direction\PosteController;
use App\Http\Controllers\Direction\SalaireController;
use App\Http\Controllers\Direction\TauxSalaireController;
use App\Http\Controllers\Direction\UserController;
use App\Http\Controllers\RapportChantierController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'premiere_connexion', 'role:direction'])
    ->prefix('direction')
    ->name('direction.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'direction'])->name('dashboard');

        // Chantiers & Affectations
        Route::middleware('permission:gerer_chantiers')->group(function () {
            Route::resource('chantiers', ChantierController::class);
            Route::prefix('chantiers/{chantier}')->name('chantiers.')->group(function () {
                Route::patch('/chef-projet', [ChantierController::class, 'affecterChefProjet'])->name('affecter-chef');
                Route::patch('/pointeur', [ChantierController::class, 'affecterPointeur'])->name('affecter-pointeur');
                Route::patch('/statut/{statut}', [ChantierController::class, 'changerStatut'])->name('statut');
            });
        });

        // Dépenses Chantier
        Route::middleware('permission:gerer_depenses')->prefix('depenses')->name('depenses.')->group(function () {
            Route::get('/', [DepenseChantierController::class, 'index'])->name('index');
            Route::get('/{chantier}', [DepenseChantierController::class, 'show'])->name('show');
            Route::post('/{chantier}', [DepenseChantierController::class, 'store'])->name('store');
            Route::delete('/{depense}', [DepenseChantierController::class, 'destroy'])->name('destroy');
        });

        // Gestion Utilisateurs
        Route::middleware('permission:gerer_utilisateurs')->prefix('utilisateurs')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}/update', [UserController::class, 'update'])->name('update');
            Route::patch('/{user}/reinitialiser', [UserController::class, 'reinitialiserMotDePasse'])->name('reinitialiser');
            Route::patch('/{user}/toggle', [UserController::class, 'toggleActif'])->name('toggle-statut');
        });

        // Postes & Personnel (Ouvriers)
        Route::middleware('permission:gerer_postes')
            ->resource('postes', PosteController::class)->except(['create', 'edit', 'show']);

        Route::middleware('permission:gerer_ouvriers')->group(function () {
            Route::resource('personnel', PersonnelController::class)->except(['show']);
            Route::patch('personnel/{personnel}/toggle', [PersonnelController::class, 'toggleStatut'])->name('personnel.toggle');
        });
        // Salaires & Taux
        Route::middleware('permission:gerer_taux_salariaux')->prefix('salaires')->name('salaires.')->group(function () {
            Route::get('/taux', [TauxSalaireController::class, 'index'])->name('taux');
            Route::get('/taux/{chantier}', [TauxSalaireController::class, 'edit'])->name('taux.edit');
            Route::put('/taux/{chantier}', [TauxSalaireController::class, 'update'])->name('taux.update');
        });
        Route::middleware('permission:gerer_salaires')->group(function () {
            Route::prefix('salaires')->name('salaires.')->group(function () {
                Route::get('/recaps', [SalaireController::class, 'index'])->name('recaps');
                Route::get('/{chantier}/apercu', [SalaireController::class, 'apercu'])->name('apercu');
                Route::get('/{chantier}/pdf', [SalaireController::class, 'genererPdf'])->name('pdf');
            });
            Route::get('/pointage/recap', [SalaireController::class, 'recapDirection'])->name('pointage.recap');
        });

        // Pointages (Récap global)
        Route::get('/pointage/recap', [SalaireController::class, 'recapDirection'])->name('pointage.recap');

        // Approvisionnements
        Route::middleware('permission:gerer_approvisionnements')->prefix('approvisionnements')->name('appro.')->group(function () {
            Route::get('/', [DirectionApproController::class, 'index'])->name('index');
            Route::get('/historique', [DirectionApproController::class, 'historique'])->name('historique');
            Route::patch('/{demande}/valider', [DirectionApproController::class, 'valider'])->name('valider');
            Route::patch('/{demande}/rejeter', [DirectionApproController::class, 'rejeter'])->name('rejeter');
            Route::patch('/{demande}/commander', [DirectionApproController::class, 'passerCommande'])->name('commander');
        });

        // Rapports
        Route::middleware('permission:voir_rapports')->group(function () {
            Route::get('/rapports', [RapportChantierController::class, 'indexDirection'])->name('rapports.index');
            Route::get('/rapports/{rapport}', [RapportChantierController::class, 'showDirection'])->name('rapports.show');
        });
    });
