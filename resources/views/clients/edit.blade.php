@extends('layouts.app')
@section('title', 'Modifier ' . $client->prenom . ' ' . $client->nom)

@section('content')
    <h2 class="mb-4">Modifier le client</h2>

    <form action="{{ route('clients.update', $client) }}" method="POST" class="card card-body">
        @method('PUT')
        @include('clients._form')
    </form>
@endsection
