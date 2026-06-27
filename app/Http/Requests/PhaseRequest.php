<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PhaseRequest extends FormRequest
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
            'nomPhase'       => 'required|string|max:255',
            'ordre'          => 'required|integer|min:1',
            'date_debut'     => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after:date_debut',
        ];
    }

    public function messages(): array
    {
        return [
            'nomPhase.required'      => 'Le nom de la phase est obligatoire.',
            'ordre.required'         => 'L\'ordre est obligatoire.',
            'date_fin_prevue.after'  => 'La date de fin doit être après la date de début.',
        ];
    }
}
