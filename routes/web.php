<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ── AUTHENTIFICATION ──────────────────────────────────────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.post');
});

Route::middleware('auth')->group(function () {

    // Changement mot de passe première connexion
    Route::get('/changer-mot-de-passe', [AuthController::class, 'showChangePassword'])
        ->name('password.change');
    Route::post('/changer-mot-de-passe', [AuthController::class, 'changePassword'])
        ->name('password.change.update');

    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // ── DASHBOARDS Direction──────────────────────────────────────────────────────────
    Route::middleware(['auth', 'premiere_connexion', 'role:direction'])->prefix('direction')->name('direction.')->group(function () {

        Route::get('/dashboard', function () {
            return view('direction.dashboard');
        })->name('dashboard');

        // Chantiers
        Route::get('/chantiers', fn() => view('direction.chantiers.index'))->name('chantiers.index');
        Route::get('/chantiers/create', fn() => view('direction.chantiers.create'))->name('chantiers.create');

        // Utilisateurs
        Route::get('/utilisateurs', fn() => view('direction.users.index'))->name('users.index');
        Route::get('/utilisateurs/create', fn() => view('direction.users.create'))->name('users.create');

        // Personnel
        Route::get('/personnel', fn() => view('direction.personnel.index'))->name('personnel.index');
        Route::get('/personnel/create', fn() => view('direction.personnel.create'))->name('personnel.create');
        Route::get('/postes', fn() => view('direction.postes.index'))->name('postes.index');

        // Salaires
        Route::get('/salaires/taux', fn() => view('direction.salaires.taux'))->name('salaires.taux');
        Route::get('/salaires/recaps', fn() => view('direction.salaires.recaps'))->name('salaires.recaps');

        // Approvisionnements
        Route::get('/approvisionnements', fn() => view('direction.appro.index'))->name('appro.index');
        Route::get('/approvisionnements/historique', fn() => view('direction.appro.historique'))->name('appro.historique');

        // Rapports
        Route::get('/rapports', fn() => view('direction.rapports.index'))->name('rapports.index');
    });

    // ── DASHBOARDS Chef de projet──────────────────────────────────────────────────────────
    Route::middleware(['auth', 'premiere_connexion', 'role:chef_projet'])
        ->prefix('chef-projet')
        ->name('chef_projet.')
        ->group(function () {
            Route::get('/dashboard', fn() => view('chef_projet.dashboard'))
                ->name('dashboard');

            Route::get('/chantiers', fn() => view('chef_projet.dashboard'))
                ->name('chantiers.index');

            Route::get('/phases', fn() => view('chef_projet.dashboard'))
                ->name('phases.index');

            Route::get('/taches', fn() => view('chef_projet.dashboard'))
                ->name('taches.index');
            Route::get('/taches/gantt', fn() => view('chef_projet.dashboard'))
                ->name('taches.gantt');

            Route::get('/pointage/validation', fn() => view('chef_projet.dashboard'))
                ->name('pointage.validation');
            Route::get('/pointage/historique', fn() => view('chef_projet.dashboard'))
                ->name('pointage.historique');
                
            Route::get('/approvisionnements', fn() => view('chef_projet.dashboard'))
                ->name('appro.index');
            Route::get('/approvisionnements/create', fn() => view('chef_projet.dashboard'))
                ->name('appro.create');
        });

    // ── DASHBOARDS Pointeur ──────────────────────────────────────────────────────────
    Route::middleware(['auth', 'premiere_connexion', 'role:pointeur'])
        ->prefix('pointeur')
        ->name('pointeur.')
        ->group(function () {
            Route::get('/dashboard', fn() => view('pointeur.dashboard'))
                ->name('dashboard');
            Route::get('/pointage/fiche', fn() => view('pointeur.dashboard'))
                ->name('pointage.fiche');
            Route::get('/pointage/historique', fn() => view('pointeur.dashboard'))
                ->name('pointage.historique');
            Route::get('/receptions/livraisons', fn() => view('pointeur.dashboard'))
                ->name('appro.livraisons');
            Route::get('/receptions/historique', fn() => view('pointeur.dashboard'))
                ->name('appro.historique');
        });
});
