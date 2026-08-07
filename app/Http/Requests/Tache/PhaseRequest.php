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
        return [
            'nomPhase'        => 'required|string|max:255',
            'ordre'           => 'required|integer|min:1',
            'typePhase'       => 'required|in:gros_oeuvre,second_oeuvre,finitions,autre',
            'sous_traitant'   => 'nullable|string|max:255',
            'date_debut'      => 'required|date',
            'date_fin_prevue' => 'required|date|after_or_equal:date_debut',
        ];
    }

    public function messages(): array
    {
        return [
            'nomPhase.required'        => 'Le nom de la phase est obligatoire.',
            'ordre.required'           => 'L\'ordre est obligatoire.',
            'typePhase.required'       => 'Le type est obligatoire.',
            'typePhase.in'             => 'Le type sélectionné est invalide.',
            'date_debut.required'      => 'La date de début est obligatoire.',
            'date_fin_prevue.required' => 'La date de fin est obligatoire.',
            'date_fin_prevue.after_or_equal' =>
            'La date de fin doit être après la date de début.',
        ];
    }
}
