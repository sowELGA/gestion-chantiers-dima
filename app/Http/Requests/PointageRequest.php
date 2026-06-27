<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PointageRequest extends FormRequest
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
            'chantier_id'        => 'required|exists:chantiers,id',
            'date'               => 'required|date|before_or_equal:today',
            'pointages'          => 'required|array',
            'pointages.*.ouvrier_id'     => 'required|exists:personnel,id',
            'pointages.*.statutPointage' => 'required|in:present,absent,maladie',
            'pointages.*.heures_sup'     => 'nullable|numeric|min:0|max:12',
        ];
    }

    public function messages(): array
    {
        return [
            'date.required'                      => 'La date est obligatoire.',
            'date.before_or_equal'               => 'La date ne peut pas être dans le futur.',
            'pointages.required'                 => 'La liste des pointages est obligatoire.',
            'pointages.*.statutPointage.required' => 'Le statut est obligatoire pour chaque ouvrier.',
            'pointages.*.statutPointage.in'      => 'Le statut sélectionné est invalide.',
            'pointages.*.heures_sup.max'         => 'Les heures supplémentaires ne peuvent pas dépasser 12h.',
        ];
    }
}
