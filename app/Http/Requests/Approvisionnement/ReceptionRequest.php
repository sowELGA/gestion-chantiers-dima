<?php

namespace App\Http\Requests\Approvisionnement;

use Illuminate\Foundation\Http\FormRequest;

class ReceptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->role === 'pointeur';
    }

    public function rules(): array
    {
        // Récupération de l'instance de la demande depuis la route (ex: /demandes/{demande}/reception)
        $demande = $this->route('demande');

        // Calcul de la quantité restante disponible à la livraison
        $quantiteRestante = $demande
            ? max(0, $demande->quantite_demandee - $demande->rapportsEntrees()->sum('quantite_recue'))
            : null;

        return [
            'quantite_recue' => [
                'required',
                'numeric',
                'gt:0',
                $quantiteRestante !== null ? "max:{$quantiteRestante}" : '',
            ],
            'observation' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'quantite_recue.required' => 'La quantité reçue est obligatoire.',
            'quantite_recue.gt'       => 'La quantité doit être strictement supérieure à 0.',
            'quantite_recue.max'      => 'La quantité reçue dépasse la quantité restante à livrer.',
        ];
    }
}
