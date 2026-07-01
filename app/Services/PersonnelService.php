<?php

namespace App\Services;

use App\Models\Personnel;

class PersonnelService
{
    public function creer(array $data): Personnel
    {
        return Personnel::create([
            'nomPersonnel'    => $data['nomPersonnel'],
            'prenomPersonnel' => $data['prenomPersonnel'],
            'statutPersonnel' => 'actif',
            'poste_id'        => $data['poste_id'],
            'chantier_id'     => $data['chantier_id'],
        ]);
    }

    public function modifier(Personnel $personnel, array $data): Personnel
    {
        $personnel->update([
            'nomPersonnel'    => $data['nomPersonnel'],
            'prenomPersonnel' => $data['prenomPersonnel'],
            'poste_id'        => $data['poste_id'],
            'chantier_id'     => $data['chantier_id'],
        ]);
        return $personnel;
    }

    public function toggleStatut(Personnel $personnel): Personnel
    {
        $nouveauStatut = $personnel->statutPersonnel === 'actif' ? 'inactif' : 'actif';
        $personnel->update(['statutPersonnel' => $nouveauStatut]);
        return $personnel;
    }
}
