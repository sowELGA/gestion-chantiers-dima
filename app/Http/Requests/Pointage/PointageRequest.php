<?php

namespace App\Http\Requests\Pointage;

use Illuminate\Foundation\Http\FormRequest;

class PointageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'pointeur';
    }

    public function rules(): array
    {
        return [
            'pointages'                  => 'required|array|min:1',
            'pointages.*.ouvrier_id'     => 'required|exists:personnels,id',
            'pointages.*.statutPointage' => 'required|in:present,absent,maladie',
            'pointages.*.heures_sup'     => 'nullable|numeric|min:0|max:12',
        ];
    }

    public function messages(): array
    {
        return [
            'pointages.required'                  => 'La fiche est vide.',
            'pointages.*.statutPointage.required'  => 'Le statut est obligatoire.',
            'pointages.*.statutPointage.in'        => 'Statut invalide.',
            'pointages.*.heures_sup.max'           => 'Max 12h supplémentaires.',
        ];
    }
}
