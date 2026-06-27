<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ChantierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->role === 'direction';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nomChantier'     => 'required|string|max:255',
            'adresse'         => 'required|string|max:255',
            'budget_prevu'    => 'required|numeric|min:0',
            'date_debut'      => 'required|date',
            'date_fin_prevue' => 'required|date|after:date_debut',
            'chef_projet_id'  => 'nullable|exists:users,id',
            'pointeur_id'     => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nomChantier.required'     => 'Le nom du chantier est obligatoire.',
            'budget_prevu.required'    => 'Le budget est obligatoire.',
            'budget_prevu.numeric'     => 'Le budget doit être un nombre.',
            'date_fin_prevue.after'    => 'La date de fin doit être après la date de début.',
        ];
    }
}
