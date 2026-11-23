<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VirementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'compte_emetteur_id'     => ['required', 'exists:comptes,id'],
            'compte_destinataire_id' => ['required', 'exists:comptes,id', 'different:compte_emetteur_id'],
            'montant'                => ['required', 'numeric', 'gt:0', 'max:999999999999', 'decimal:0,2'],
        ];
    }

    public function messages(): array
    {
        return [
            'compte_emetteur_id.required'     => 'Le compte émetteur est obligatoire.',
            'compte_emetteur_id.exists'       => "Le compte émetteur n'existe pas.",
            'compte_destinataire_id.required' => 'Le compte destinataire est obligatoire.',
            'compte_destinataire_id.exists'   => "Le compte destinataire n'existe pas.",
            'compte_destinataire_id.different' => "Le compte destinataire doit être différent du compte émetteur.",
            'montant.required'                => 'Le montant est obligatoire.',
            'montant.numeric'                 => 'Le montant doit être un nombre.',
            'montant.gt'                      => 'Le montant doit être supérieur à 0.',
            'montant.max'                     => 'Le montant est trop élevé.',
            'montant.decimal'                 => 'Le montant ne peut avoir que 2 décimales au maximum.',
        ];
    }
}
