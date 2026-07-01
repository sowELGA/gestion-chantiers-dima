<?php

namespace App\Http\Requests\Chantier;

use Illuminate\Foundation\Http\FormRequest;

class ChantierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'direction';
    }

    public function rules(): array
    {
        return [
            'nomChantier'     => 'required|string|max:255',
            'adresse'         => 'required|string|max:255',
            'budget_prevu'    => 'required|numeric|min:1',
            'date_debut'      => 'required|date',
            'date_fin_prevue' => 'required|date|after:date_debut',
            'chef_projet_id'  => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nomChantier.required'     => 'Le nom du chantier est obligatoire.',
            'adresse.required'         => 'L\'adresse est obligatoire.',
            'budget_prevu.required'    => 'Le budget est obligatoire.',
            'budget_prevu.numeric'     => 'Le budget doit être un nombre.',
            'budget_prevu.min'         => 'Le budget doit être supérieur à 0.',
            'date_debut.required'      => 'La date de début est obligatoire.',
            'date_fin_prevue.required' => 'La date de fin est obligatoire.',
            'date_fin_prevue.after'    => 'La date de fin doit être après la date de début.',
        ];
    }
}