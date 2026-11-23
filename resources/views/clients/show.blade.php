@extends('layouts.app')
@section('title', $client->prenom . ' ' . $client->nom)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>{{ $client->prenom }} {{ $client->nom }}</h2>
        <a href="{{ session('retour_clients', route('clients.index')) }}" class="btn btn-outline-secondary">← Retour</a>
    </div>

    <div class="card card-body mb-4">
        <p class="mb-1"><strong>CIN :</strong> {{ $client->cin }}</p>
        <p class="mb-1"><strong>Téléphone :</strong> {{ $client->telephone }}</p>
        <p class="mb-1"><strong>Email :</strong> {{ $client->email }}</p>
        <p class="mb-0"><strong>Adresse :</strong> {{ $client->adresse ?: '—' }}</p>
    </div>

    <h4>Comptes de ce client</h4>
    <div class="card">
        <table class="table mb-0 align-middle">
            <thead class="table-dark">
                <tr><th>RIB</th><th class="text-end">Solde</th><th class="text-end">Détail</th></tr>
            </thead>
            <tbody>
                @forelse ($client->comptes as $compte)
                    <tr>
                        <td class="font-monospace">{{ $compte->rib_formate }}</td>
                        <td class="text-end fw-semibold {{ $compte->solde_classe }}">{{ $compte->solde_formate }}</td>
                        <td class="text-end">
                            <a href="{{ route('comptes.show', $compte) }}" class="btn btn-sm btn-outline-primary">Voir</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted py-3">Aucun compte.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
