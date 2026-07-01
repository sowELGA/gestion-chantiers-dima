<?php

namespace App\Http\Requests\Personnel;

use Illuminate\Foundation\Http\FormRequest;

class PosteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'direction';
    }

    public function rules(): array
    {
        $posteId = $this->route('poste')?->id;

        return [
            'libelle' => 'required|string|max:255|unique:postes,libelle,' . $posteId . ',id',
        ];
    }

    public function messages(): array
    {
        return [
            'libelle.required' => 'Le libellé du poste est obligatoire.',
            'libelle.unique'   => 'Ce poste existe déjà.',
        ];
    }
}
