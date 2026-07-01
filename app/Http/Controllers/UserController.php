<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    // Liste des utilisateurs
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())
            ->orderBy('nomUser')
            ->get()
            ->groupBy('role');

        return view('direction.users.index', compact('users'));
    }

    // Formulaire création
    public function create()
    {
        return view('direction.users.create');
    }

    // Enregistrer un utilisateur
    public function store(UserRequest $request)
    {
        $result = $this->userService->creer($request->validated());

        return redirect()
            ->route('direction.users.index')
            ->with('success', 'Compte créé avec succès.')
            ->with('mot_de_passe', $result['motDePasseTemp'])
            ->with('user_nom', $result['user']->nomComplet);
    }

    // Formulaire édition
    public function edit(User $user)
    {
        return view('direction.users.edit', compact('user'));
    }

    // Mettre à jour un utilisateur
    public function update(UserRequest $request, User $user)
    {
        $this->userService->modifier($user, $request->validated());

        return redirect()
            ->route('direction.users.index')
            ->with('success', 'Compte mis à jour avec succès.');
    }

    // Activer / Désactiver
    public function toggleStatut(User $user)
    {
        $this->userService->toggleStatut($user);

        $message = $user->fresh()->actif
            ? 'Compte activé avec succès.'
            : 'Compte désactivé avec succès.';

        return back()->with('success', $message);
    }

    // Réinitialiser le mot de passe
    public function reinitialiserMotDePasse(User $user)
    {
        $motDePasseTemp = $this->userService->reinitialiserMotDePasse($user);

        return back()
            ->with('success', 'Mot de passe réinitialisé avec succès.')
            ->with('mot_de_passe', $motDePasseTemp)
            ->with('user_nom', $user->nomComplet);
    }
}
