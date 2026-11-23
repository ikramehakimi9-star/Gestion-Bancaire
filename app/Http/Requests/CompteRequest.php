<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $compte = $this->route('compte');
        $compteId = is_object($compte) ? $compte->id : $compte;

        return [
            'rib'       => ['required', 'digits:24', Rule::unique('comptes', 'rib')->ignore($compteId)],
            'solde'     => ['nullable', 'numeric', 'min:0'],
            'client_id' => ['required', 'exists:clients,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'rib.required'       => 'Le RIB est obligatoire.',
            'rib.digits'         => 'Le RIB doit contenir exactement 24 chiffres.',
            'rib.unique'         => 'Ce RIB existe déjà.',
            'solde.numeric'      => 'Le solde doit être un nombre.',
            'solde.min'          => 'Le solde ne peut pas être négatif.',
            'client_id.required' => 'Le client est obligatoire.',
            'client_id.exists'   => "Ce client n'existe pas.",
        ];
    }
}
