<?php

namespace App\Http\Requests\Approvisionnement;

use Illuminate\Foundation\Http\FormRequest;

class ApprovisionnementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'chef_projet';
    }

    public function rules(): array
    {
        return [
            'designation'           => 'required|string|max:255',
            'quantite_demandee'     => 'required|numeric|min:0.1',
            'unite'                 => 'required|string|max:50',
            'priorite'              => 'nullable|in:normal,urgent',
            'chantier_id'           => 'required|exists:chantiers,id',
            'date_livraison_souhaitee' => 'required|date|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'designation.required'       => 'La désignation est obligatoire.',
            'quantite_demandee.required' => 'La quantité est obligatoire.',
            'quantite_demandee.min'      => 'La quantité doit être supérieure à 0.',
            'unite.required'             => 'L\'unité est obligatoire.',
            'chantier_id.required'       => 'Le chantier est obligatoire.',
            'date_livraison_souhaitee.required'       => 'La date de livraison souhaitée est obligatoire.',
            'date_livraison_souhaitee.after_or_equal' =>
                'La date de livraison doit être aujourd\'hui ou dans le futur.',
        ];
    }
}