@extends('layouts.app')
@section('title', 'Historique des virements')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Historique des virements</h2>
        <a href="{{ route('virements.create') }}" class="btn btn-primary">+ Nouveau virement</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Émetteur</th>
                        <th>Destinataire</th>
                        <th class="text-end">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($virements as $virement)
                        <tr>
                            <td>{{ $virement->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                {{ $virement->emetteur->client->nom }} {{ $virement->emetteur->client->prenom }}<br>
                                <small class="text-muted font-monospace">{{ $virement->emetteur->rib_formate }}</small>
                            <td>
                                {{ $virement->destinataire->client->nom }} {{ $virement->destinataire->client->prenom }}<br>
                                <small class="text-muted font-monospace">{{ $virement->destinataire->rib_formate }}</small>
                            </td>
                            <td class="text-end fw-bold">
                                {{ number_format((float) $virement->montant, 2, ',', ' ') }} DH
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="fs-5 mb-1">Aucun virement effectué</div>
                                <p class="text-muted mb-3">Les virements apparaîtront ici dès le premier transfert.</p>
                                <a href="{{ route('virements.create') }}" class="btn btn-primary">Effectuer un premier virement</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $virements->links() }}</div>
@endsection
