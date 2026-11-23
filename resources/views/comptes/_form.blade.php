@csrf

<div class="mb-3">
    <label for="client_id" class="form-label">Client</label>
    <select name="client_id" id="client_id"
            class="form-select @error('client_id') is-invalid @enderror">
        <option value="">-- Choisir un client --</option>
        @foreach ($clients as $client)
            <option value="{{ $client->id }}"
                {{ old('client_id', $compte->client_id ?? '') == $client->id ? 'selected' : '' }}>
                {{ $client->nom }} {{ $client->prenom }} ({{ $client->cin }})
            </option>
        @endforeach
    </select>
    @error('client_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="rib" class="form-label">RIB (24 chiffres)</label>
    <input type="text" name="rib" id="rib" maxlength="24"
           class="form-control @error('rib') is-invalid @enderror"
           value="{{ old('rib', $compte->rib ?? '') }}">
    @error('rib')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="solde" class="form-label">Solde (DH)</label>
    <input type="number" step="0.01" min="0" name="solde" id="solde"
           class="form-control @error('solde') is-invalid @enderror"
           value="{{ old('solde', $compte->solde ?? 0) }}">
    @error('solde')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">Enregistrer</button>
    <a href="{{ session('retour_comptes', route('comptes.index')) }}" class="btn btn-outline-secondary">Annuler</a>
</div>
