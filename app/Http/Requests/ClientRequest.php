<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Lors d'une modification, on ignore le client en cours
        // pour que son propre CIN / email ne soit pas considéré comme doublon.
        $client = $this->route('client');
        $clientId = is_object($client) ? $client->id : $client;

        return [
            'nom'       => ['required', 'string', 'max:255'],
            'prenom'    => ['required', 'string', 'max:255'],
            'cin'       => ['required', 'string', 'max:20', Rule::unique('clients', 'cin')->ignore($clientId)],
            'telephone' => ['required', 'string', 'max:20'],
            'email'     => ['required', 'email', 'max:255', Rule::unique('clients', 'email')->ignore($clientId)],
            'adresse'   => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required'       => 'Le nom est obligatoire.',
            'prenom.required'    => 'Le prénom est obligatoire.',
            'cin.required'       => 'Le CIN est obligatoire.',
            'cin.unique'         => 'Ce CIN existe déjà.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'email.required'     => "L'email est obligatoire.",
            'email.email'        => "L'email n'est pas valide.",
            'email.unique'       => 'Cet email existe déjà.',
        ];
    }
}
