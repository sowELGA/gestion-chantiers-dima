<?php

namespace App\Services;

use App\Models\Poste;

class PosteService
{
    public function creer(array $data): Poste
    {
        return Poste::create([
            'libelle' => $data['libelle'],
        ]);
    }

    public function modifier(Poste $poste, array $data): Poste
    {
        $poste->update([
            'libelle' => $data['libelle'],
        ]);
        return $poste;
    }

    public function supprimer(Poste $poste): void
    {
        if ($poste->personnel()->exists()) {
            throw new \Exception(
                'Impossible de supprimer ce poste : du personnel y est rattaché.'
            );
        }
        $poste->delete();
    }
}
