@extends('layouts.app')
@section('title', 'Nouveau client')

@section('content')
    <h2 class="mb-4">Ajouter un client</h2>

    <form action="{{ route('clients.store') }}" method="POST" class="card card-body">
        @include('clients._form')
    </form>
@endsection
