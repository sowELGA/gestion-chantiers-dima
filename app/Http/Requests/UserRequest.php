<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'direction';
    }

    public function rules(): array
    {
        // En édition on exclut l'utilisateur actuel de l'unicité email
        $id = $this->route('user')?->id;

        return [
            'nomUser'    => 'required|string|max:255',
            'prenomUser' => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $id . ',id',
            'telUser' => 'required|string|max:20',
            'role'       => 'required|in:direction,chef_projet,pointeur',
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nomUser.required'    => 'Le nom est obligatoire.',
            'prenomUser.required' => 'Le prénom est obligatoire.',
            'telUser.required' => 'Le téléphone est obligatoire.',
            'email.required'      => 'L\'email est obligatoire.',
            'email.email'         => 'L\'email n\'est pas valide.',
            'email.unique'        => 'Cet email est déjà utilisé.',
            'role.required'       => 'Le rôle est obligatoire.',
            'role.in'             => 'Le rôle sélectionné est invalide.',
        ];
    }
}
