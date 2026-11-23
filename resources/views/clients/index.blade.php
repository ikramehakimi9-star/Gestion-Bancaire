@extends('layouts.app')
@section('title', 'Clients')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Liste des clients</h2>
        <a href="{{ route('clients.create') }}" class="btn btn-primary">+ Ajouter un client</a>
    </div>

    {{-- Recherche --}}
    <form method="GET" action="{{ route('clients.index') }}" class="mb-3">
        <div class="input-group" style="max-width: 560px;">
            <input type="search" name="q" value="{{ $q }}" autocomplete="off"
                   class="form-control" placeholder="Rechercher : nom, prénom, CIN, email, téléphone">
            <button class="btn btn-primary" type="submit">Rechercher</button>
            @if ($q !== '')
                <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">Effacer</a>
            @endif
        </div>
    </form>

    @if ($q !== '')
        <p class="text-muted">{{ $clients->total() }} résultat(s) pour « {{ $q }} »</p>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>CIN</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                        <th>Comptes</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clients as $client)
                        <tr>
                            <td>{{ $client->nom }}</td>
                            <td>{{ $client->prenom }}</td>
                            <td>{{ $client->cin }}</td>
                            <td>{{ $client->telephone }}</td>
                            <td>{{ $client->email }}</td>
                            <td><span class="badge bg-secondary">{{ $client->comptes_count }}</span></td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-primary">Voir</a>
                                <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-secondary">Modifier</a>
                                <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm(@js('Supprimer ' . $client->prenom . ' ' . $client->nom . ' et tous ses comptes ?'))">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                @if ($q !== '')
                                    <div class="fs-5 mb-1">Aucun client ne correspond à « {{ $q }} »</div>
                                    <p class="text-muted mb-3">Vérifiez l'orthographe ou essayez avec moins de lettres.</p>
                                    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">Afficher tous les clients</a>
                                @else
                                    <div class="fs-5 mb-1">Aucun client enregistré</div>
                                    <p class="text-muted mb-3">Commencez par ajouter votre premier client.</p>
                                    <a href="{{ route('clients.create') }}" class="btn btn-primary">+ Ajouter un client</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $clients->links() }}</div>
@endsection
