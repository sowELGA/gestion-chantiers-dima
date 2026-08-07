<?php

namespace App\Http\Requests\Tache;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Phase;

class TacheRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $phase = $this->route('phase');

        return [
            'nomTache'            => 'required|string|max:255',
            'date_debut_prevue'   => [
                'required',
                'date',
                $phase?->date_debut
                    ? 'after_or_equal:' . $phase->date_debut->format('Y-m-d')
                    : '',
            ],
            'date_fin_prevue'     => [
                'required',
                'date',
                'after_or_equal:date_debut_prevue',
                $phase?->date_fin_prevue
                    ? 'before_or_equal:' . $phase->date_fin_prevue->format('Y-m-d')
                    : '',
            ],
            'tache_precedente_id' => 'nullable|exists:taches,id',
        ];
    }

    public function messages(): array
    {
        $phase = $this->route('phase');

        return [
            'nomTache.required'              => 'Le nom de la tâche est obligatoire.',
            'date_debut_prevue.required'     => 'La date de début est obligatoire.',
            'date_debut_prevue.after_or_equal' =>
            'La date de début doit être après ou égale au début de la phase'
                . ($phase?->date_debut
                    ? ' (' . $phase->date_debut->format('d/m/Y') . ')'
                    : '') . '.',
            'date_fin_prevue.required'       => 'La date de fin est obligatoire.',
            'date_fin_prevue.after_or_equal' =>
            'La date de fin doit être après la date de début.',
            'date_fin_prevue.before_or_equal' =>
            'La date de fin doit être avant la fin de la phase'
                . ($phase?->date_fin_prevue
                    ? ' (' . $phase->date_fin_prevue->format('d/m/Y') . ')'
                    : '') . '.',
        ];
    }
}
