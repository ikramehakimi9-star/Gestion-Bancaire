<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Accueil') | Gestion Bancaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">Gestion Bancaire</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}"
                       href="{{ route('clients.index') }}">Clients</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('comptes.*') ? 'active' : '' }}"
                       href="{{ route('comptes.index') }}">Comptes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('virements.*') ? 'active' : '' }}"
                       href="{{ route('virements.index') }}">Virements</a>
                </li>
            </ul>
            <a href="{{ route('virements.create') }}" class="btn btn-sm btn-outline-light">+ Nouveau virement</a>
        </div>
    </div>
</nav>

<main class="container pb-5 flex-grow-1">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>
                Veuillez corriger {{ $errors->count() > 1 ? 'les ' . $errors->count() . ' erreurs suivantes' : "l'erreur suivante" }} :
            </strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $erreur)
                    <li>{{ $erreur }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @yield('content')
</main>

<footer class="bg-dark text-light text-center py-3 mt-auto small">

</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Indicateur de chargement : tout formulaire POST bloque son bouton et affiche une roue
document.addEventListener('submit', function (e) {
    const form = e.target;
    if (e.defaultPrevented || form.hasAttribute('data-no-loading')) return;
    if ((form.method || '').toLowerCase() !== 'post') return;

    const btn = form.querySelector('button[type="submit"], button:not([type])');
    if (!btn || btn.disabled) return;

    // setTimeout : on laisse d'abord le navigateur envoyer le formulaire
    setTimeout(function () {
        btn.dataset.label = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Traitement…';
    }, 0);
});

// Si l'utilisateur revient en arrière avec le bouton du navigateur, on réactive les boutons
window.addEventListener('pageshow', function (e) {
    if (!e.persisted) return;
    document.querySelectorAll('button[data-label]').forEach(function (b) {
        b.disabled = false;
        b.innerHTML = b.dataset.label;
    });
});
</script>
@stack('scripts')
</body>
</html>
