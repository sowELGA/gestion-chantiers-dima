<?php

namespace App\Http\Controllers;

use App\Mail\DemandeResetNotifMail;
use App\Models\DemandeResetMdp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'L\'email est obligatoire.',
            'email.email'       => 'L\'email n\'est pas valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Identifiants incorrects.']);
        }

        $user = Auth::user();

        if (!$user->actif) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Votre compte est désactivé. Contactez la direction.',
            ]);
        }

        $request->session()->regenerate();

        if ($user->premiere_connexion) {
            return redirect()->route('password.change');
        }

        return redirect()->intended(match ($user->role) {
            'direction'   => route('direction.dashboard'),
            'chef_projet' => route('chef_projet.dashboard'),
            'pointeur'    => route('pointeur.dashboard'),
            default       => '/',
        });
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ── Mot de passe oublié ───────────────────────────────────

    public function motDePasseOublie()
    {
        return view('auth.mot-de-passe-oublie');
    }

    public function signalerOubli(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'L\'email est obligatoire.',
            'email.email'    => 'Format d\'email invalide.',
            'email.exists'   => 'Aucun compte trouvé avec cet email.',
        ]);

        // Éviter les doublons
        $dejaEnAttente = DemandeResetMdp::where('email', $request->email)
            ->where('statut', 'en_attente')
            ->exists();

        if ($dejaEnAttente) {
            return back()->with(
                'success',
                'Une demande est déjà en cours. La direction la traitera bientôt.'
            );
        }

        $demande = DemandeResetMdp::create([
            'email'  => $request->email,
            'statut' => 'en_attente',
        ]);

        // Notifier la direction par email
        $userDemandeur = User::where('email', $request->email)->first();
        $directions    = User::where('role', 'direction')->where('actif', true)->where('est_super_admin', true)->get();

        foreach ($directions as $dir) {
            Mail::to($dir->email)->send(
                new DemandeResetNotifMail($userDemandeur, $demande)
            );
        }

        return back()->with(
            'success',
            'Votre demande a été transmise à la direction. '
                . 'Vous recevrez un email avec vos nouveaux identifiants.'
        );
    }

    // ── Changement de mot de passe (première connexion) ───────

    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation ne correspond pas.',
        ]);

        $user = auth()->user();
        $user->update([
            'password'           => bcrypt($request->password),
            'premiere_connexion' => false,
        ]);

        return redirect()->intended(match ($user->role) {
            'direction'   => route('direction.dashboard'),
            'chef_projet' => route('chef_projet.dashboard'),
            'pointeur'    => route('pointeur.dashboard'),
            default       => '/',
        })->with('success', 'Mot de passe mis à jour avec succès.');
    }
}
