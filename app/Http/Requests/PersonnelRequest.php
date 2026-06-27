<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PersonnelRequest extends FormRequest
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
        ];
    }
}
