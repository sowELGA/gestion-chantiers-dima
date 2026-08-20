<?php

use App\Http\Controllers\ApprovisionnementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChantierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepenseChantierController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\PointageController;
use App\Http\Controllers\PosteController;
use App\Http\Controllers\RecapHebdomadaireController;
use App\Http\Controllers\TacheController;
use App\Http\Controllers\TauxSalaireController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('login');
});

// ── AUTHENTIFICATION ──────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.post');
    Route::get(
        '/mot-de-passe-oublie',
        [AuthController::class, 'motDePasseOublie']
    )->name('password.oublie');
    Route::post(
        '/mot-de-passe-oublie',
        [AuthController::class, 'signalerOubli']
    )->name('password.oublie.signaler');
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
    Route::middleware(['auth', 'premiere_connexion', 'role:direction'])
        ->prefix('direction')->name('direction.')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'direction'])
                ->name('dashboard');

            // Chantiers
            Route::get('/chantiers', [ChantierController::class, 'index'])
                ->name('chantiers.index');
            Route::get('/chantiers/create', [ChantierController::class, 'create'])
                ->name('chantiers.create');
            Route::post('/chantiers', [ChantierController::class, 'store'])
                ->name('chantiers.store');
            Route::get('/chantiers/{chantier}', [ChantierController::class, 'show'])
                ->name('chantiers.show');
            Route::get('/chantiers/{chantier}/edit', [ChantierController::class, 'edit'])
                ->name('chantiers.edit');
            Route::put('/chantiers/{chantier}', [ChantierController::class, 'update'])
                ->name('chantiers.update');
            Route::delete('/chantiers/{chantier}', [ChantierController::class, 'destroy'])
                ->name('chantiers.destroy');

            // Affectations
            Route::patch(
                '/chantiers/{chantier}/chef-projet',
                [ChantierController::class, 'affecterChefProjet']
            )
                ->name('chantiers.affecter-chef');
            Route::patch(
                '/chantiers/{chantier}/pointeur',
                [ChantierController::class, 'affecterPointeur']
            )
                ->name('chantiers.affecter-pointeur');

            // Statut
            Route::patch(
                '/chantiers/{chantier}/statut/{statut}',
                [ChantierController::class, 'changerStatut']
            )
                ->name('chantiers.statut');

            // Dépenses
            Route::get('/depenses', [DepenseChantierController::class, 'index'])
                ->name('depenses.index');
            Route::get('/depenses/{chantier}', [DepenseChantierController::class, 'show'])
                ->name('depenses.show');
            Route::post('/depenses/{chantier}', [DepenseChantierController::class, 'store'])
                ->name('depenses.store');
            Route::delete(
                '/depenses/{depense}',
                [DepenseChantierController::class, 'destroy']
            )
                ->name('depenses.destroy');

            // Utilisateurs
            Route::get(
                '/utilisateurs',
                [UserController::class, 'index']
            )->name('users.index');
            Route::get(
                '/utilisateurs/create',
                [UserController::class, 'create']
            )->name('users.create');
            Route::post(
                '/utilisateurs',
                [UserController::class, 'store']
            )->name('users.store');
            Route::get(
                '/utilisateurs/edit',
                [UserController::class, 'edit']
            )->name('users.edit');
            Route::patch(
                '/utilisateurs/{user}/update',
                [UserController::class, 'update']
            )->name('users.update');
            Route::patch(
                '/utilisateurs/{user}/reinitialiser',
                [UserController::class, 'reinitialiserMotDePasse']
            )->name('users.reinitialiser');
            Route::patch(
                '/utilisateurs/{user}/toggle',
                [UserController::class, 'toggleActif']
            )->name('users.toggle-statut');

            // Postes
            Route::get('/postes', [PosteController::class, 'index'])
                ->name('postes.index');
            Route::post('/postes', [PosteController::class, 'store'])
                ->name('postes.store');
            Route::put('/postes/{poste}', [PosteController::class, 'update'])
                ->name('postes.update');
            Route::delete('/postes/{poste}', [PosteController::class, 'destroy'])
                ->name('postes.destroy');

            // Personnel
            Route::resource('personnel', PersonnelController::class)->except(['show']);
            Route::patch(
                'personnel/{personnel}/toggle',
                [PersonnelController::class, 'toggleStatut']
            )
                ->name('personnel.toggle');

            Route::get('/salaires/taux', [TauxSalaireController::class, 'index'])
                ->name('salaires.taux');
            Route::get('/salaires/taux/{chantier}', [TauxSalaireController::class, 'edit'])
                ->name('salaires.taux.edit');
            Route::put('/salaires/taux/{chantier}', [TauxSalaireController::class, 'update'])
                ->name('salaires.taux.update');

            Route::get(
                '/pointage/recap',
                [RecapHebdomadaireController::class, 'recapDirection']
            )
                ->name('pointage.recap');

            // Fiches de paie
            Route::get('/salaires/recaps', [RecapHebdomadaireController::class, 'index'])
                ->name('salaires.recaps');

            Route::get(
                '/salaires/{chantier}/apercu',
                [RecapHebdomadaireController::class, 'apercu']
            )
                ->name('salaires.apercu');
            Route::get(
                '/salaires/{chantier}/pdf',
                [RecapHebdomadaireController::class, 'genererPdf']
            )
                ->name('salaires.pdf');
            // Approvisionnements
            Route::get(
                '/approvisionnements',
                [ApprovisionnementController::class, 'indexDirection']
            )
                ->name('appro.index');
            Route::patch(
                '/approvisionnements/{demande}/valider',
                [ApprovisionnementController::class, 'valider']
            )
                ->name('appro.valider');
            Route::patch(
                '/approvisionnements/{demande}/rejeter',
                [ApprovisionnementController::class, 'rejeter']
            )
                ->name('appro.rejeter');
            Route::patch(
                '/approvisionnements/{demande}/commander',
                [ApprovisionnementController::class, 'passerCommande']
            )
                ->name('appro.commander');
            Route::get(
                '/approvisionnements/historique',
                [ApprovisionnementController::class, 'historique']
            )
                ->name('appro.historique');
            Route::get(
                '/approvisionnements/bon-entree/{rapport}/pdf',
                [ApprovisionnementController::class, 'bonEntreePdf']
            )
                ->name('appro.bon-entree-pdf');
            // Rapports
            Route::get('/rapports', fn() => view('direction.rapports.index'))->name('rapports.index');
        });

    // ── DASHBOARDS Chef de projet──────────────────────────────────────────────────────────
    Route::middleware(['auth', 'premiere_connexion', 'role:chef_projet'])
        ->prefix('chef-projet')->name('chef_projet.')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'chefProjet'])
                ->name('dashboard');

            // Chantiers
            Route::get('/chantiers', [ChantierController::class, 'indexChefProjet'])
                ->name('chantiers.index');
            Route::get('/chantiers/{chantier}', [ChantierController::class, 'showChefProjet'])
                ->name('chantiers.show');

            // ── Phases ────────────────────────────────────────────────
            Route::get(
                '/chantiers/{chantier}/phases',
                [TacheController::class, 'indexPhases']
            )->name('phases.index');
            Route::get(
                '/chantiers/{chantier}/phases/create',
                [TacheController::class, 'createPhase']
            )->name('phases.create');
            Route::post(
                '/chantiers/{chantier}/phases',
                [TacheController::class, 'storePhase']
            )->name('phases.store');
            Route::get(
                '/chantiers/{chantier}/phases/{phase}/edit',
                [TacheController::class, 'editPhase']
            )->name('phases.edit');
            Route::patch(
                '/chantiers/{chantier}/phases/{phase}',
                [TacheController::class, 'updatePhase']
            )->name('phases.update');
            Route::delete(
                '/chantiers/{chantier}/phases/{phase}',
                [TacheController::class, 'destroyPhase']
            )->name('phases.destroy');

            // Tâches (toujours dans le contexte d'une phase)
            Route::get(
                '/chantiers/{chantier}/phases/{phase}/taches',
                [TacheController::class, 'indexTaches']
            )->name('taches.index');
            Route::get(
                '/chantiers/{chantier}/phases/{phase}/taches/create',
                [TacheController::class, 'createTache']
            )->name('taches.create');
            Route::post(
                '/chantiers/{chantier}/phases/{phase}/taches',
                [TacheController::class, 'storeTache']
            )->name('taches.store');
            Route::get(
                '/chantiers/{chantier}/phases/{phase}/taches/{tache}/edit',
                [TacheController::class, 'editTache']
            )->name('taches.edit');
            Route::patch(
                '/chantiers/{chantier}/phases/{phase}/taches/{tache}',
                [TacheController::class, 'updateTache']
            )->name('taches.update');
            Route::delete(
                '/chantiers/{chantier}/phases/{phase}/taches/{tache}',
                [TacheController::class, 'destroyTache']
            )->name('taches.destroy');
            Route::patch(
                '/chantiers/{chantier}/phases/{phase}/taches/{tache}/avancement',
                [TacheController::class, 'mettreAJourAvancement']
            )->name('taches.avancement');

            // Gantt
            Route::get(
                '/chantiers/{chantier}/gantt',
                [TacheController::class, 'gantt']
            )->name('taches.gantt');

            //Pointage
            Route::get(
                '/chantiers/{chantier}/pointage',
                [RecapHebdomadaireController::class, 'validationChefProjet']
            )
                ->name('pointage.validation');
            Route::post(
                '/chantiers/{chantier}/pointage/valider',
                [RecapHebdomadaireController::class, 'valider']
            )
                ->name('pointage.valider');
            Route::post(
                '/chantiers/{chantier}/pointage/rejeter',
                [RecapHebdomadaireController::class, 'rejeter']
            )
                ->name('pointage.rejeter');

            //approvisionnements
            Route::get(
                '/approvisionnements/create',
                [ApprovisionnementController::class, 'create']
            )
                ->name('appro.create');
            Route::post(
                '/approvisionnements',
                [ApprovisionnementController::class, 'store']
            )
                ->name('appro.store');
            Route::get(
                '/approvisionnements',
                [ApprovisionnementController::class, 'indexChefProjet']
            )
                ->name('appro.index');
            Route::get(
                '/approvisionnements/{chantier}/{demande}/edit',
                [ApprovisionnementController::class, 'edit']
            )
                ->name('appro.edit');

            Route::patch(
                '/approvisionnements/{chantier}/{demande}',
                [ApprovisionnementController::class, 'update']
            )
                ->name('appro.update');

            Route::delete(
                '/approvisionnements/{chantier}/{demande}',
                [ApprovisionnementController::class, 'destroy']
            )
                ->name('appro.destroy');
        });

    // ── DASHBOARDS Pointeur ──────────────────────────────────────────────────────────
    Route::middleware(['auth', 'premiere_connexion', 'role:pointeur'])
        ->prefix('pointeur')->name('pointeur.')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'pointeur'])
                ->name('dashboard');

            // Fiche journalière
            Route::get('/pointage/fiche',  [PointageController::class, 'ficheJour'])
                ->name('pointage.fiche');
            Route::post('/pointage/fiche', [PointageController::class, 'enregistrerFiche'])
                ->name('pointage.enregistrer');

            // Récap semaine
            Route::get('/pointage/recap',    [PointageController::class, 'recapSemaine'])
                ->name('pointage.recap');
            Route::post('/pointage/soumettre', [PointageController::class, 'soumettreSemaine'])
                ->name('pointage.soumettre');

            // Modification par jour (récap rejeté) — 1 jour = 1 page
            Route::get('/pointage/modifier/{date}', [PointageController::class, 'modifierJour'])
                ->name('pointage.modifier-jour')
                ->where('date', '\d{4}-\d{2}-\d{2}');
            Route::post('/pointage/modifier', [PointageController::class, 'enregistrerModificationJour'])
                ->name('pointage.enregistrer-modification');

            //Approvisionnements
            Route::get(
                '/receptions/livraisons',
                [ApprovisionnementController::class, 'livraisons']
            )
                ->name('appro.livraisons');
            Route::post(
                '/receptions/{demande}/valider',
                [ApprovisionnementController::class, 'validerReception']
            )
                ->name('appro.reception');
            Route::get(
                '/receptions/bon-entree/{rapport}/pdf',
                [ApprovisionnementController::class, 'bonEntreePdf']
            )
                ->name('appro.bon-entree-pdf');
            Route::get(
                '/receptions/historique',
                [ApprovisionnementController::class, 'historiqueLivraisons']
            )
                ->name('appro.historique');
        });
});
