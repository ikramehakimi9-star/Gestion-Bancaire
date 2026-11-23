@extends('layouts.app')
@section('title', 'Nouveau compte')

@section('content')
    <h2 class="mb-4">Ajouter un compte</h2>

    <form action="{{ route('comptes.store') }}" method="POST" class="card card-body">
        @include('comptes._form')
    </form>
@endsection
