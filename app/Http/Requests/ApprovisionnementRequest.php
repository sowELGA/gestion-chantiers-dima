<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ApprovisionnementRequest extends FormRequest
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
            'designation'       => 'required|string|max:255',
            'quantite_demandee' => 'required|numeric|min:0.1',
            'unite'             => 'required|string|max:50',
            'priorite'          => 'required|in:normal,urgent',
            'chantier_id'       => 'required|exists:chantiers,id',
        ];
    }

    public function messages(): array
    {
        return [
            'designation.required'       => 'La désignation est obligatoire.',
            'quantite_demandee.required' => 'La quantité est obligatoire.',
            'quantite_demandee.min'      => 'La quantité doit être supérieure à 0.',
            'unite.required'             => 'L\'unité est obligatoire.',
            'priorite.required'          => 'La priorité est obligatoire.',
            'priorite.in'                => 'La priorité doit être normal ou urgent.',
        ];
    }
}
