<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Mail\CompteCreeMail;
use App\Mail\MdpReinitialiseMail;
use App\Models\DemandeResetMdp;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Mail;

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

        $demandesReset = DemandeResetMdp::where('statut', 'en_attente')
            ->orderBy('created_at', 'desc')
            ->get();

        return view(
            'direction.users.index',
            compact('users', 'demandesReset')
        );
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
        $lien       = config('app.url');

        try {
            Mail::to($result['user']->email)->send(
                new CompteCreeMail($result['user'], $result['motDePasseTemp'], $lien)
            );
            $emailEnvoye = true;
        } catch (\Exception $e) {
            $emailEnvoye = false;
        }

        return redirect()
            ->route('direction.users.index')
            ->with('compte_cree', [
                'nom'          => $result['user']->prenomUser . ' ' . $result['user']->nomUser,
                'email'        => $result['user']->email,
                'mot_passe'    => $result['motDePasseTemp'],
                'email_envoye' => $emailEnvoye,
            ]);
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
        $nouveauMdp = $this->userService->reinitialiserMotDePasse($user);
        $lien       = config('app.url');

        DemandeResetMdp::where('email', $user->email)
            ->where('statut', 'en_attente')
            ->update([
                'statut'     => 'traitee',
                'traite_le'  => now(),
            ]);

        try {
            Mail::to($user->email)->send(
                new MdpReinitialiseMail($user, $nouveauMdp, $lien)
            );
            $emailEnvoye = true;
        } catch (\Exception $e) {
            $emailEnvoye = false;
        }

        return back()->with('mdp_reinitialise', [
            'nom'          => $user->prenomUser . ' ' . $user->nomUser,
            'email'        => $user->email,
            'mot_passe'    => $nouveauMdp,
            'email_envoye' => $emailEnvoye,
        ]);
    }
}
