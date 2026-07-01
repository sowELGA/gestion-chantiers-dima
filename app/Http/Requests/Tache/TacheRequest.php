<?php

namespace App\Http\Requests\Tache;

use Illuminate\Foundation\Http\FormRequest;

class TacheRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'chef_projet';
    }

    public function rules(): array
    {
        return [
            'nomTache'            => 'required|string|max:255',
            'type'                => 'required|in:gros_oeuvre,second_oeuvre',
            'descriptionTache'    => 'nullable|string',
            'besoins_materiels'   => 'nullable|string',
            'besoins_materiaux'   => 'nullable|string',
            'date_debut_prevue'   => 'required|date',
            'date_fin_prevue'     => 'required|date|after_or_equal:date_debut_prevue',
            'sous_traitant'       => 'nullable|string|max:255',
            'phase_id'            => 'required|exists:phases,id',
            'responsable_id'      => 'nullable|exists:users,id',
            'tache_precedente_id' => 'nullable|exists:taches,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nomTache.required'          => 'Le nom de la tâche est obligatoire.',
            'type.required'              => 'Le type est obligatoire.',
            'type.in'                    => 'Le type sélectionné est invalide.',
            'date_debut_prevue.required' => 'La date de début est obligatoire.',
            'date_fin_prevue.required'   => 'La date de fin est obligatoire.',
            'date_fin_prevue.after_or_equal' =>
            'La date de fin doit être après ou égale à la date de début.',
            'phase_id.required'          => 'La phase est obligatoire.',
            'phase_id.exists'            => 'La phase sélectionnée est invalide.',
        ];
    }
}
