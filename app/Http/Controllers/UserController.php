<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function reinitialiserMotDePasse(User $user)
    {
        // Générer un mot de passe temporaire
        $motDePasseTemp = 'Dima@' . rand(1000, 9999);

        $user->update([
            'password'           => Hash::make($motDePasseTemp),
            'premiere_connexion' => true,
        ]);

        return back()->with([
            'success'        => 'Mot de passe réinitialisé avec succès.',
            'mot_de_passe'   => $motDePasseTemp,
            'user_nom'       => $user->nomComplet,
        ]);
    }
}
