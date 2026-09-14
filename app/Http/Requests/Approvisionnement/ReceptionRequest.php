<?php

namespace App\Http\Requests\Approvisionnement;

use App\Models\Chantier;
use Illuminate\Foundation\Http\FormRequest;

class ReceptionRequest extends FormRequest
{
    /**
     * Vérifiait auparavant seulement le rôle ("pointeur"), jamais que le
     * pointeur connecté est bien responsable du CHANTIER auquel
     * appartient la demande ($this->route('demande')). N'importe quel
     * pointeur authentifié pouvait donc réceptionner une livraison
     * destinée à un chantier qu'il ne gère pas, simplement en connaissant
     * ou devinant l'ID de la demande dans l'URL.
     */
    public function authorize(): bool
    {
        if (auth()->user()?->role !== 'pointeur') {
            return false;
        }

        $demande = $this->route('demande');

        if (!$demande) {
            return false;
        }

        return Chantier::where('pointeur_id', auth()->id())
            ->where('id', $demande->chantier_id)
            ->exists();
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
            'quantite_recue' => array_filter([
                'required',
                'numeric',
                'gt:0',
                $quantiteRestante !== null ? "max:{$quantiteRestante}" : null,
            ]),
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
