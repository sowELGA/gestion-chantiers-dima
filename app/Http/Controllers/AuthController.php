<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Afficher la page de connexion
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectParRole();
        }
        return view('auth.login');
    }

    // Traiter la connexion
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Email ou mot de passe incorrect.',
                ]);
        }

        $request->session()->regenerate();

        // Vérifier première connexion
        if (Auth::user()->premiere_connexion) {
            return redirect()->route('password.change');
        }

        return $this->redirectParRole();
    }

    // Afficher la page changement mot de passe
    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    // Traiter le changement de mot de passe
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();

        $user->update([
            'password'           => Hash::make($request->password),
            'premiere_connexion' => false,
        ]);

        return $this->redirectParRole();
    }

    // Déconnexion
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // Redirection selon le rôle
    private function redirectParRole()
    {
        return match(Auth::user()->role) {
            'direction'   => redirect()->route('direction.dashboard'),
            'chef_projet' => redirect()->route('chef_projet.dashboard'),
            'pointeur'    => redirect()->route('pointeur.dashboard'),
            default       => redirect()->route('login'),
        };
    }
}