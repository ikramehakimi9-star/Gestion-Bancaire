@extends('layouts.app')
@section('title', 'Tableau de bord')

@section('content')
    <h2 class="mb-4">Tableau de bord</h2>

    {{-- Cartes de statistiques --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-lg-2">
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="text-muted small">Clients</div>
                    <div class="fs-3 fw-bold">{{ $stats['clients'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="text-muted small">Comptes</div>
                    <div class="fs-3 fw-bold">{{ $stats['comptes'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="text-muted small">Virements</div>
                    <div class="fs-3 fw-bold">{{ $stats['virements'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="text-muted small">Virements aujourd'hui</div>
                    <div class="fs-3 fw-bold">{{ $stats['virements_aujourdhui'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card text-center h-100 border-success">
                <div class="card-body">
                    <div class="text-muted small">Solde total</div>
                    <div class="fs-5 fw-bold text-success">{{ number_format($stats['solde_total'], 2, ',', ' ') }} DH</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card text-center h-100 border-primary">
                <div class="card-body">
                    <div class="text-muted small">Montant viré</div>
                    <div class="fs-5 fw-bold text-primary">{{ number_format($stats['montant_total'], 2, ',', ' ') }} DH</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Derniers virements --}}
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Derniers virements</strong>
                    <a href="{{ route('virements.index') }}" class="btn btn-sm btn-outline-secondary">Tout voir</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>De</th>
                                <th>Vers</th>
                                <th class="text-end">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($derniersVirements as $v)
                                <tr>
                                    <td>{{ $v->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $v->emetteur->client->nom }} {{ $v->emetteur->client->prenom }}</td>
                                    <td>{{ $v->destinataire->client->nom }} {{ $v->destinataire->client->prenom }}</td>
                                    <td class="text-end">{{ number_format((float) $v->montant, 2, ',', ' ') }} DH</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">Aucun virement pour le moment.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Comptes avec le plus grand solde --}}
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Comptes les mieux dotés</strong>
                    <a href="{{ route('comptes.index') }}" class="btn btn-sm btn-outline-secondary">Tout voir</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th class="text-end">Solde</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topComptes as $c)
                                <tr>
                                    <td>
                                    {{ $c->client->nom }} {{ $c->client->prenom }}<br>
                                    <small class="text-muted font-monospace">{{ $c->rib_formate }}</small>
                                    </td>
                                    <td class="text-end fw-semibold {{ $c->solde_classe }}">{{ $c->solde_formate }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted py-3">Aucun compte.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Accès rapides --}}
    <div class="mt-4 d-flex gap-2">
        <a href="{{ route('virements.create') }}" class="btn btn-primary">+ Nouveau virement</a>
        <a href="{{ route('clients.create') }}" class="btn btn-outline-primary">+ Nouveau client</a>
        <a href="{{ route('comptes.create') }}" class="btn btn-outline-primary">+ Nouveau compte</a>
    </div>
@endsection
