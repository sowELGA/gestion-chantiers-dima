<?php

use App\Http\Controllers\Pointeur\DashboardController;
use App\Http\Controllers\Pointeur\PointageController;
use App\Http\Controllers\Pointeur\ReceptionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'premiere_connexion', 'role:pointeur'])
    ->prefix('pointeur')
    ->name('pointeur.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'pointeur'])->name('dashboard');

        // Pointages journaliers et hebdomadaires
        Route::prefix('pointage')
            ->name('pointage.')
            ->group(function () {
                Route::get('/fiche', [PointageController::class, 'ficheJour'])->name('fiche');
                Route::post('/fiche', [PointageController::class, 'enregistrerFiche'])->name('enregistrer-fiche');

                Route::get('/recap', [PointageController::class, 'recapSemaine'])->name('recap');
                Route::post('/soumettre', [PointageController::class, 'soumettreSemaine'])->name('soumettre');

                Route::get('/modifier/{date}', [PointageController::class, 'modifierJour'])
                    ->name('modifier-jour')
                    ->where('date', '\d{4}-\d{2}-\d{2}');
                Route::post('/modifier', [PointageController::class, 'enregistrerModificationJour'])->name('enregistrer-modification');
            });

        // Réceptions Livraisons
        Route::prefix('receptions')->name('appro.')->group(function () {
            Route::get('/livraisons', [ReceptionController::class, 'livraisons'])->name('livraisons');
            Route::get('/historique', [ReceptionController::class, 'historiqueLivraisons'])->name('historique');
            Route::post('/{demande}/valider', [ReceptionController::class, 'validerReception'])->name('reception');
            Route::get('/bon-entree/{rapport}/pdf', [ReceptionController::class, 'bonEntreePdf'])->name('bon-entree-pdf');
        });
    });
