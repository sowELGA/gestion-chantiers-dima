<?php

namespace App\Http\Requests\Approvisionnement;

use Illuminate\Foundation\Http\FormRequest;

class ReceptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'pointeur';
    }

    public function rules(): array
    {
        return [
            'quantite_recue' => 'required|numeric|min:0.1',
            'observation'    => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'quantite_recue.required' => 'La quantité reçue est obligatoire.',
            'quantite_recue.min'      => 'La quantité doit être supérieure à 0.',
        ];
    }
}
