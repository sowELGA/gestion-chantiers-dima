<?php

namespace App\Services;

use App\Models\Ouvrier;

class PersonnelService
{
    public function creer(array $data): Ouvrier
    {
        return Ouvrier::create([
            'nomOuvrier'    => $data['nomOuvrier'],
            'prenomOuvrier' => $data['prenomOuvrier'],
            'telOuvrier'    => $data['telOuvrier'],
            'statutOuvrier' => 'actif',
            'poste_id'      => $data['poste_id'],
            'chantier_id'   => $data['chantier_id'],
        ]);
    }

    public function modifier(Ouvrier $personnel, array $data): Ouvrier
    {
        $personnel->update([
            'nomOuvrier'    => $data['nomOuvrier'],
            'prenomOuvrier' => $data['prenomOuvrier'],
            'telOuvrier'    => $data['telOuvrier'],
            'poste_id'      => $data['poste_id'],
            'chantier_id'   => $data['chantier_id'],
        ]);

        return $personnel->fresh();
    }

    public function toggleStatut(Ouvrier $personnel): Ouvrier
    {
        $personnel->update([
            'statutOuvrier' => $personnel->statutOuvrier === 'actif'
                ? 'inactif'
                : 'actif',
        ]);

        return $personnel->refresh();
    }
}
