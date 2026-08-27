<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// ── AUTHENTIFICATION ──────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/mot-de-passe-oublie', [AuthController::class, 'motDePasseOublie'])->name('password.oublie');
    Route::post('/mot-de-passe-oublie', [AuthController::class, 'signalerOubli'])->name('password.oublie.signaler');
});

Route::middleware('auth')->group(function () {

    // Changement mot de passe première connexion
    Route::get('/changer-mot-de-passe', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/changer-mot-de-passe', [AuthController::class, 'changePassword'])->name('password.change.update');

    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ── CHARGEMENT DES ROUTES PAR RÔLE ─────────────────────────────────────────
    require __DIR__ . '/roles/direction.php';
    require __DIR__ . '/roles/chef_projet.php';
    require __DIR__ . '/roles/pointeur.php';
});
