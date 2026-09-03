<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RapportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre'        => 'required|string|max:255',
            'date_rapport' => 'required|date',
            'type'         => 'required|in:avancement,incident,livraison,reunion,autre',
            'contenu'      => 'required|string|min:10',
            'chantier_id'  => 'required|exists:chantiers,id',
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required'        => 'Le titre est obligatoire.',
            'date_rapport.required' => 'La date est obligatoire.',
            'type.required'         => 'Le type de rapport est obligatoire.',
            'contenu.required'      => 'Le contenu est obligatoire.',
            'contenu.min'           => 'Le contenu doit contenir au moins 10 caractères.',
            'chantier_id.required'  => 'Le chantier est obligatoire.',
        ];
    }
}
