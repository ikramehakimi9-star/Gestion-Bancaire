@extends('layouts.app')
@section('title', 'Comptes')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Liste des comptes</h2>
        <a href="{{ route('comptes.create') }}" class="btn btn-primary">+ Ajouter un compte</a>
    </div>

    {{-- Recherche --}}
    <form method="GET" action="{{ route('comptes.index') }}" class="mb-3">
        <div class="input-group" style="max-width: 560px;">
            <input type="search" name="q" value="{{ $q }}" autocomplete="off"
                   class="form-control" placeholder="Rechercher : RIB, nom, prénom ou CIN du titulaire">
            <button class="btn btn-primary" type="submit">Rechercher</button>
            @if ($q !== '')
                <a href="{{ route('comptes.index') }}" class="btn btn-outline-secondary">Effacer</a>
            @endif
        </div>
    </form>

    @if ($q !== '')
        <p class="text-muted">{{ $comptes->total() }} résultat(s) pour « {{ $q }} »</p>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>RIB</th>
                        <th>Client</th>
                        <th class="text-end">Solde</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($comptes as $compte)
                        <tr>
                            <td class="font-monospace">{{ $compte->rib_formate }}</td>
                            <td>{{ $compte->client->nom }} {{ $compte->client->prenom }}</td>
                            <td class="text-end fw-semibold {{ $compte->solde_classe }}">{{ $compte->solde_formate }}</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('comptes.show', $compte) }}" class="btn btn-sm btn-outline-primary">Voir</a>
                                <a href="{{ route('comptes.edit', $compte) }}" class="btn btn-sm btn-outline-secondary">Modifier</a>
                                <form action="{{ route('comptes.destroy', $compte) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm(@js('Supprimer le compte ' . $compte->rib_formate . ' ?'))">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                @if ($q !== '')
                                    <div class="fs-5 mb-1">Aucun compte ne correspond à « {{ $q }} »</div>
                                    <p class="text-muted mb-3">Essayez avec une partie du RIB ou le nom du titulaire.</p>
                                    <a href="{{ route('comptes.index') }}" class="btn btn-outline-secondary">Afficher tous les comptes</a>
                                @else
                                    <div class="fs-5 mb-1">Aucun compte enregistré</div>
                                    <p class="text-muted mb-3">Ajoutez un compte pour pouvoir effectuer des virements.</p>
                                    <a href="{{ route('comptes.create') }}" class="btn btn-primary">+ Ajouter un compte</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $comptes->links() }}</div>
@endsection
