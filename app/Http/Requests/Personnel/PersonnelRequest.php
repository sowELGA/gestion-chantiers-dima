<?php

namespace App\Http\Requests\Personnel;

use Illuminate\Foundation\Http\FormRequest;

class PersonnelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'direction';
    }

    public function rules(): array
    {
        return [
            'nomPersonnel'    => 'required|string|max:255',
            'prenomPersonnel' => 'required|string|max:255',
            'poste_id'        => 'required|exists:postes,id',
            'chantier_id'     => 'required|exists:chantiers,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nomPersonnel.required'    => 'Le nom est obligatoire.',
            'prenomPersonnel.required' => 'Le prénom est obligatoire.',
            'poste_id.required'        => 'Le poste est obligatoire.',
            'poste_id.exists'          => 'Le poste sélectionné est invalide.',
            'chantier_id.required'     => 'Le chantier est obligatoire.',
            'chantier_id.exists'       => 'Le chantier sélectionné est invalide.',
        ];
    }
}
