@extends('layouts.app')
@section('title', 'Compte ' . $compte->rib_formate)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Compte <span class="font-monospace">{{ $compte->rib_formate }}</span></h2>
        <a href="{{ session('retour_comptes', route('comptes.index')) }}" class="btn btn-outline-secondary">← Retour</a>
    </div>

    <div class="card card-body mb-4">
        <p class="mb-1"><strong>RIB :</strong> <span class="font-monospace">{{ $compte->rib_formate }}</span></p>
        <p class="mb-1">
            <strong>Solde :</strong>
            <span class="fw-bold fs-5 {{ $compte->solde_classe }}">{{ $compte->solde_formate }}</span>
        </p>
        <p class="mb-0">
            <strong>Titulaire :</strong>
            <a href="{{ route('clients.show', $compte->client) }}">
                {{ $compte->client->prenom }} {{ $compte->client->nom }}
            </a>
        </p>
    </div>

    <h4>Virements émis</h4>
    <div class="card mb-4">
        <table class="table table-sm mb-0">
            <thead class="table-dark">
                <tr><th>Date</th><th>Vers</th><th class="text-end">Montant</th></tr>
            </thead>
            <tbody>
                @forelse ($compte->virementsEmis->sortByDesc('created_at') as $v)
                    <tr>
                        <td>{{ $v->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $v->destinataire->client->nom }} {{ $v->destinataire->client->prenom }}</td>
                        <td class="text-end text-danger">− {{ number_format((float) $v->montant, 2, ',', "\u{00A0}") }}&nbsp;DH</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted py-3">Aucun virement émis.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h4>Virements reçus</h4>
    <div class="card">
        <table class="table table-sm mb-0">
            <thead class="table-dark">
                <tr><th>Date</th><th>De</th><th class="text-end">Montant</th></tr>
            </thead>
            <tbody>
                @forelse ($compte->virementsRecus->sortByDesc('created_at') as $v)
                    <tr>
                        <td>{{ $v->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $v->emetteur->client->nom }} {{ $v->emetteur->client->prenom }}</td>
                        <td class="text-end text-success">+ {{ number_format((float) $v->montant, 2, ',', "\u{00A0}") }}&nbsp;DH</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted py-3">Aucun virement reçu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
