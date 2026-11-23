@extends('layouts.app')
@section('title', 'Modifier le compte')

@section('content')
    <h2 class="mb-4">Modifier le compte</h2>

    <form action="{{ route('comptes.update', $compte) }}" method="POST" class="card card-body">
        @method('PUT')
        @include('comptes._form')
    </form>
@endsection
