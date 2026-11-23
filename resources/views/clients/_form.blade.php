@csrf

@php
    $champs = [
        'nom'       => ['Nom', 'text'],
        'prenom'    => ['Prénom', 'text'],
        'cin'       => ['CIN', 'text'],
        'telephone' => ['Téléphone', 'text'],
        'email'     => ['Email', 'email'],
        'adresse'   => ['Adresse', 'text'],
    ];
@endphp

@foreach ($champs as $name => [$label, $type])
    <div class="mb-3">
        <label for="{{ $name }}" class="form-label">{{ $label }}</label>
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}"
               class="form-control @error($name) is-invalid @enderror"
               value="{{ old($name, $client->$name ?? '') }}">
        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
@endforeach

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">Enregistrer</button>
    <a href="{{ session('retour_clients', route('clients.index')) }}" class="btn btn-outline-secondary">Annuler</a>
</div>
