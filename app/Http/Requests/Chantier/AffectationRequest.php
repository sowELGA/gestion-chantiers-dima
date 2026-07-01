<?php

namespace App\Http\Requests\Chantier;

use Illuminate\Foundation\Http\FormRequest;

class AffectationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'direction';
    }

    public function rules(): array
    {
        return [
            'chef_projet_id' => 'nullable|exists:users,id',
            'pointeur_id'    => 'nullable|exists:users,id',
        ];
    }
}
