@extends('layouts.app')
@section('title', 'Nouveau virement')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Nouveau virement</h2>
        <a href="{{ route('virements.index') }}" class="btn btn-outline-secondary">← Historique</a>
    </div>

    @if ($comptes->count() < 2)
        <div class="alert alert-warning">
            Il faut au moins deux comptes pour effectuer un virement.
            <a href="{{ route('comptes.create') }}">Ajouter un compte</a>
        </div>
    @else
        <form id="form-virement" action="{{ route('virements.store') }}" method="POST"
              class="card card-body" autocomplete="off" data-no-loading>
            @csrf

            <div class="mb-3">
                <label for="compte_emetteur_id" class="form-label">Compte à débiter <span class="text-danger">*</span></label>
                <select name="compte_emetteur_id" id="compte_emetteur_id" required
                        class="form-select @error('compte_emetteur_id') is-invalid @enderror">
                    <option value="">-- Choisir le compte émetteur --</option>
                    @foreach ($comptes as $compte)
                        <option value="{{ $compte->id }}"
                                data-solde="{{ $compte->solde }}"
                                data-label="{{ $compte->client->nom }} {{ $compte->client->prenom }} — {{ $compte->rib }}"
                            {{ old('compte_emetteur_id') == $compte->id ? 'selected' : '' }}>
                            {{ $compte->client->nom }} {{ $compte->client->prenom }} — {{ $compte->rib }}
                        </option>
                    @endforeach
                </select>
                <div id="info-emetteur" class="form-text fw-semibold"></div>
                @error('compte_emetteur_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="compte_destinataire_id" class="form-label">Compte à créditer <span class="text-danger">*</span></label>
                <select name="compte_destinataire_id" id="compte_destinataire_id" required
                        class="form-select @error('compte_destinataire_id') is-invalid @enderror">
                    <option value="">-- Choisir le compte destinataire --</option>
                    @foreach ($comptes as $compte)
                        <option value="{{ $compte->id }}"
                                data-solde="{{ $compte->solde }}"
                                data-label="{{ $compte->client->nom }} {{ $compte->client->prenom }} — {{ $compte->rib }}"
                            {{ old('compte_destinataire_id') == $compte->id ? 'selected' : '' }}>
                            {{ $compte->client->nom }} {{ $compte->client->prenom }} — {{ $compte->rib }}
                        </option>
                    @endforeach
                </select>
                @error('compte_destinataire_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="montant" class="form-label">Montant (DH) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0.01" name="montant" id="montant" required
                       class="form-control @error('montant') is-invalid @enderror"
                       value="{{ old('montant') }}" placeholder="0,00">
                <div id="info-montant" class="form-text fw-semibold"></div>
                @error('montant')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="button" id="btn-valider" class="btn btn-primary">Vérifier le virement</button>
                <a href="{{ route('virements.index') }}" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>

        {{-- Fenêtre de confirmation --}}
        <div class="modal fade" id="modal-confirmation" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmer le virement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-3">Vérifiez les informations avant de valider. Cette opération est définitive.</p>
                        <dl class="row mb-0">
                            <dt class="col-sm-4">De</dt>
                            <dd class="col-sm-8" id="recap-de"></dd>
                            <dt class="col-sm-4">Vers</dt>
                            <dd class="col-sm-8" id="recap-vers"></dd>
                            <dt class="col-sm-4">Montant</dt>
                            <dd class="col-sm-8 fw-bold fs-5" id="recap-montant"></dd>
                            <dt class="col-sm-4">Solde après</dt>
                            <dd class="col-sm-8" id="recap-apres"></dd>
                        </dl>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Modifier</button>
                        <button type="button" id="btn-confirmer" class="btn btn-success">Confirmer le virement</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form       = document.getElementById('form-virement');
    if (!form) return;

    const emetteur     = document.getElementById('compte_emetteur_id');
    const destinataire = document.getElementById('compte_destinataire_id');
    const montant      = document.getElementById('montant');
    const infoEm       = document.getElementById('info-emetteur');
    const infoMontant  = document.getElementById('info-montant');
    const btnValider   = document.getElementById('btn-valider');
    const btnConfirmer = document.getElementById('btn-confirmer');
    const modalEl      = document.getElementById('modal-confirmation');
    let confirme = false;

    const fmt = n => new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 2, maximumFractionDigits: 2
    }).format(n) + ' DH';

    const option = sel => sel.options[sel.selectedIndex];
    const solde  = () => emetteur.value ? parseFloat(option(emetteur).dataset.solde) : null;
    const valeur = () => parseFloat(montant.value);

    function mettreAJour() {
        // Solde disponible de l'émetteur
        const s = solde();
        infoEm.textContent = s === null ? '' : 'Solde disponible : ' + fmt(s);
        infoEm.className = 'form-text fw-semibold text-secondary';

        // On ne peut pas choisir le même compte des deux côtés
        Array.from(destinataire.options).forEach(o => {
            o.disabled = o.value !== '' && o.value === emetteur.value;
        });
        if (destinataire.value && destinataire.value === emetteur.value) {
            destinataire.value = '';
        }

        // Aperçu du solde après virement
        const m = valeur();
        if (s !== null && m > 0) {
            const apres = s - m;
            if (apres < 0) {
                infoMontant.textContent = 'Solde insuffisant : il manque ' + fmt(-apres) + '.';
                infoMontant.className = 'form-text fw-semibold text-danger';
            } else {
                infoMontant.textContent = 'Solde après virement : ' + fmt(apres);
                infoMontant.className = 'form-text fw-semibold text-success';
            }
        } else {
            infoMontant.textContent = '';
        }
    }

    [emetteur, destinataire, montant].forEach(el => el.addEventListener('input', mettreAJour));
    mettreAJour();

    function ouvrirConfirmation() {
        if (!form.reportValidity()) return;

        const s = solde(), m = valeur();
        if (s !== null && m > s) {
            mettreAJour();
            montant.focus();
            return;
        }

        document.getElementById('recap-de').textContent      = option(emetteur).dataset.label;
        document.getElementById('recap-vers').textContent    = option(destinataire).dataset.label;
        document.getElementById('recap-montant').textContent = fmt(m);
        document.getElementById('recap-apres').textContent   = fmt(s - m);

        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }

    btnValider.addEventListener('click', ouvrirConfirmation);

    // La touche Entrée passe aussi par la confirmation
    form.addEventListener('submit', function (e) {
        if (!confirme) {
            e.preventDefault();
            ouvrirConfirmation();
        }
    });

    // Anti double clic : le bouton se bloque dès la confirmation
    btnConfirmer.addEventListener('click', function () {
        confirme = true;
        btnConfirmer.disabled = true;
        btnConfirmer.textContent = 'Traitement en cours…';
        form.submit();
    });
});
</script>
@endpush
