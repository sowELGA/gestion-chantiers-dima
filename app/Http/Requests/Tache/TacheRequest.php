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
        // Récupérer la phase pour vérifier la date de début
        $phaseId = $this->input('phase_id');
        $phase   = $phaseId ? \App\Models\Phase::find($phaseId) : null;
        $dateDebutMinPhase = $phase?->date_debut?->toDateString() ?? null;

        return [
            'nomTache'            => 'required|string|max:255',
            'type'                => 'required|in:gros_oeuvre,second_oeuvre',
            'descriptionTache'    => 'nullable|string',
            'besoins_materiels'   => 'nullable|string',
            'besoins_materiaux'   => 'nullable|string',
            'date_debut_prevue'   => [
                'required',
                'date',
                // Date début tâche >= Date début phase
                $dateDebutMinPhase ? 'after_or_equal:' . $dateDebutMinPhase : '',
            ],
            'date_fin_prevue'     => 'required|date|after_or_equal:date_debut_prevue',
            'sous_traitant'       => 'nullable|string|max:255',
            'phase_id'            => 'required|exists:phases,id',
            'responsable_id'      => 'nullable|exists:users,id',
            'tache_precedente_id' => 'nullable|exists:taches,id',
        ];
    }

    public function messages(): array
    {
        $phaseId = $this->input('phase_id');
        $phase   = $phaseId ? \App\Models\Phase::find($phaseId) : null;

        return [
            'nomTache.required'              => 'Le nom de la tâche est obligatoire.',
            'type.required'                  => 'Le type est obligatoire.',
            'type.in'                        => 'Le type sélectionné est invalide.',
            'date_debut_prevue.required'     => 'La date de début est obligatoire.',
            'date_debut_prevue.after_or_equal' =>
            'La date de début de la tâche ne peut pas être antérieure à la date de début
             de la phase' . ($phase?->date_debut
                ? ' (' . $phase->date_debut->format('d/m/Y') . ')'
                : '') . '.',
            'date_fin_prevue.required'       => 'La date de fin est obligatoire.',
            'date_fin_prevue.after_or_equal' => 'La date de fin doit être après la date de début.',
            'phase_id.required'              => 'La phase est obligatoire.',
        ];
    }
}
