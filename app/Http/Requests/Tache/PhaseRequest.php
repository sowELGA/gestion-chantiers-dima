<?php

namespace App\Http\Requests\Tache;

use Illuminate\Foundation\Http\FormRequest;

class PhaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'chef_projet';
    }

    public function rules(): array
    {
        return [
            'nomPhase'       => 'required|string|max:255',
            'ordre'          => 'nullable|integer|min:1',
            'date_debut'     => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after_or_equal:date_debut',
        ];
    }

    public function messages(): array
    {
        return [
            'nomPhase.required'       => 'Le nom de la phase est obligatoire.',
            'ordre.integer'           => 'L\'ordre doit être un nombre entier.',
            'date_fin_prevue.after_or_equal' =>
            'La date de fin doit être après ou égale à la date de début.',
        ];
    }
}
