<?php

namespace App\Http\Requests\Pointage;

use Illuminate\Foundation\Http\FormRequest;

class HeuresSupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'pointeur';
    }

    public function rules(): array
    {
        return [
            'ouvrier_id' => 'required|exists:personnels,id',
            'heures_sup' => 'required|numeric|min:0|max:12',
        ];
    }

    public function messages(): array
    {
        return [
            'heures_sup.required' => 'Les heures supplémentaires sont obligatoires.',
            'heures_sup.numeric'  => 'Doit être un nombre.',
            'heures_sup.min'      => 'Ne peut pas être négatif.',
            'heures_sup.max'      => 'Ne peut pas dépasser 12h.',
        ];
    }
}
