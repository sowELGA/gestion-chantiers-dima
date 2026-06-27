<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TacheRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nomTache'         => 'required|string|max:255',
            'type'             => 'required|in:gros_oeuvre,second_oeuvre',
            'descriptionTache' => 'nullable|string',
            'besoins_materiels' => 'nullable|string',
            'besoins_materiaux' => 'nullable|string',
            'date_debut_prevue' => 'required|date',
            'date_fin_prevue'  => 'required|date|after:date_debut_prevue',
            'responsable_id'   => 'nullable|exists:users,id',
            'phase_id'         => 'required|exists:phases_chantier,i',
            'sous_traitant'    => 'nullable|string|max:255',
            'tache_precedente_id' => 'nullable|exists:taches,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nomTache.required'       => 'Le nom de la tâche est obligatoire.',
            'type.required'           => 'Le type de tâche est obligatoire.',
            'date_debut_prevue.required' => 'La date de début est obligatoire.',
            'date_fin_prevue.after'   => 'La date de fin doit être après la date de début.',
            'phase_id.required'       => 'La phase est obligatoire.',
        ];
    }
}
