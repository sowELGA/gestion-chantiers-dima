<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    // Créer un utilisateur
    public function creer(array $data): array
    {
        $motDepasseTemp = 'Dima@' . rand(1000, 9999);

        $user = User::create([
            'nomUser'           => $data['nomUser'],
            'prenomUser'        => $data['prenomUser'],
            'email'             => $data['email'],
            'password'          => Hash::make($motDepasseTemp),
            'role'              => $data['role'],
            'premiere_connexion' => true
        ]);

        return [
            'user'             => $user,
            'motDePasseTemp'   => $motDepasseTemp
        ];
    }

    // Modifier un utilisateur
    public function modifier(User $user, array $data): array
    {
        $user->update([
            'nomUser'           => $data['nomUser'],
            'prenomUser'        => $data['prenomUser'],
            'email'             => $data['email'],
            'role'              => $data['role']
        ]);

        return [
            'user'             => $user,
        ];
    }

    // Activer / Désactiver un compte
    public function toggleStatut(User $user): User
    {
        // On utilise premiere_connexion comme indicateur de statut
        // On ajoute un champ actif dans la migration
        $user->update([
            'actif' => !$user->actif,
        ]);

        return $user;
    }

    // Réinitialiser le mot de passe
    public function reinitialiserMotDePasse(User $user): string
    {
        $motDePasseTemp = 'Dima@' . rand(1000, 9999);

        $user->update([
            'password'           => Hash::make($motDePasseTemp),
            'premiere_connexion' => true,
        ]);

        return $motDePasseTemp;
    }
}
