<?php

namespace App\Http\Requests\Chantier;

use App\Models\Chantier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class AffectationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'direction';
    }

    public function rules(): array
    {
        return [
            'chef_projet_id' => [
                'nullable',
                Rule::exists('users', 'id')
                    ->where('role', 'chef_projet')
                    ->where('actif', true),
            ],
            'pointeur_id' => [
                'nullable',
                Rule::exists('users', 'id')
                    ->where('role', 'pointeur')
                    ->where('actif', true),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'chef_projet_id.exists' => 'Le chef de projet sélectionné est invalide ou inactif.',
            'pointeur_id.exists'    => 'Le pointeur sélectionné est invalide ou inactif.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (!$this->filled('pointeur_id')) {
                return;
            }

            $chantier   = $this->route('chantier');
            $pointeurId = (int) $this->input('pointeur_id');

            $dejaAssigneAilleurs = Chantier::where('pointeur_id', $pointeurId)
                ->when($chantier, fn($q) => $q->where('id', '!=', $chantier->id))
                ->exists();

            if ($dejaAssigneAilleurs) {
                $validator->errors()->add(
                    'pointeur_id',
                    'Ce pointeur est déjà affecté à un autre chantier.'
                );
            }
        });
    }
}
