<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RapportRequest extends FormRequest
{
    /**
     * Vérifiait auparavant toujours true, sans contrôle de rôle — contrairement
     * à tous les autres FormRequest de l'application. Seul le middleware de
     * route empêchait un autre rôle d'atteindre ce formulaire ; ce contrôle
     * redondant protège même si les routes sont un jour réorganisées.
     */
    public function authorize(): bool
    {
        return auth()->user()?->role === 'chef_projet';
    }

    public function rules(): array
    {
        return [
            'titre'        => 'required|string|max:255',
            'date_rapport' => 'required|date',
            'type'         => 'required|in:avancement,incident,livraison,reunion,autre',
            'contenu'      => 'required|string|min:10',
            'chantier_id'  => [
                'required',
                Rule::exists('chantiers', 'id')->where('chef_projet_id', auth()->id()),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required'        => 'Le titre est obligatoire.',
            'date_rapport.required' => 'La date est obligatoire.',
            'type.required'         => 'Le type de rapport est obligatoire.',
            'contenu.required'      => 'Le contenu est obligatoire.',
            'contenu.min'           => 'Le contenu doit contenir au moins 10 caractères.',
            'chantier_id.required'  => 'Le chantier est obligatoire.',
            'chantier_id.exists'    => 'Ce chantier ne vous est pas affecté.',
        ];
    }
}
