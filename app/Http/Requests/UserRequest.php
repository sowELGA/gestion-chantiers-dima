<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'nomUser'    => 'required|string|max:255',
            'prenomUser' => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $id . ',id',
            'role'       => 'required|in:direction,chef_projet,pointeur',
        ];
    }

    public function messages(): array
    {
        return [
            'nomUser.required'    => 'Le nom est obligatoire.',
            'prenomUser.required' => 'Le prénom est obligatoire.',
            'email.required'      => 'L\'email est obligatoire.',
            'email.email'         => 'L\'email n\'est pas valide.',
            'email.unique'        => 'Cet email est déjà utilisé.',
            'role.required'       => 'Le rôle est obligatoire.',
            'role.in'             => 'Le rôle sélectionné est invalide.',
        ];
    }
}
