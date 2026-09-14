<?php

namespace App\Http\Requests\Tache;

use Illuminate\Foundation\Http\FormRequest;

class PhaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $chantier = $this->route('chantier');

        return [
            'nomPhase'      => 'required|string|max:255',
            'ordre'         => 'required|integer|min:1',
            'typePhase'     => 'required|in:gros_oeuvre,second_oeuvre,finitions,autre',
            'sous_traitant' => 'nullable|string|max:255',
            // Bornées aux dates du chantier — même logique que TacheRequest
            // qui borne une tâche aux dates de sa phase parente. Ce
            // garde-fou manquait ici : rien n'empêchait de créer une
            // phase en dehors de la période du chantier.
            'date_debut' => array_filter([
                'required',
                'date',
                $chantier?->date_debut
                    ? 'after_or_equal:' . $chantier->date_debut->format('Y-m-d')
                    : null,
            ]),
            'date_fin_prevue' => array_filter([
                'required',
                'date',
                'after_or_equal:date_debut',
                $chantier?->date_fin_prevue
                    ? 'before_or_equal:' . $chantier->date_fin_prevue->format('Y-m-d')
                    : null,
            ]),
        ];
    }

    public function messages(): array
    {
        $chantier = $this->route('chantier');

        return [
            'nomPhase.required'        => 'Le nom de la phase est obligatoire.',
            'ordre.required'           => 'L\'ordre est obligatoire.',
            'typePhase.required'       => 'Le type est obligatoire.',
            'typePhase.in'             => 'Le type sélectionné est invalide.',
            'date_debut.required'      => 'La date de début est obligatoire.',
            'date_debut.after_or_equal' =>
            'La date de début doit être après ou égale au début du chantier'
                . ($chantier?->date_debut
                    ? ' (' . $chantier->date_debut->format('d/m/Y') . ')'
                    : '') . '.',
            'date_fin_prevue.required' => 'La date de fin est obligatoire.',
            'date_fin_prevue.after_or_equal' =>
            'La date de fin doit être après la date de début.',
            'date_fin_prevue.before_or_equal' =>
            'La date de fin doit être avant la fin prévue du chantier'
                . ($chantier?->date_fin_prevue
                    ? ' (' . $chantier->date_fin_prevue->format('d/m/Y') . ')'
                    : '') . '.',
        ];
    }
}
