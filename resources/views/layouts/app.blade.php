<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PNAE-RCA V10 Enterprise')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

<header class="topbar">
    <a href="{{ route('home') }}" class="brand">
        <div class="flag-mini"></div>
        <div>
            <strong>PNAE-RCA V10</strong>
            <span>République Centrafricaine</span>
        </div>
    </a>

    <nav class="main-nav">
        <a href="{{ route('home') }}">Accueil</a>
        <a href="{{ route('services') }}">Services</a>
        <a href="#">Le PNAE</a>
        <a href="{{ route('contact') }}">Contact</a>

        @auth
            <a href="{{ route('citizen.dashboard') }}">Mon espace</a>

            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit">Déconnexion</button>
            </form>
        @else
            <a href="{{ route('login') }}">Connexion</a>
            <a class="account-btn" href="{{ route('register') }}">Créer un compte</a>
        @endauth
    </nav>
</header>

@if(session('success'))
    <div class="container mt-3">
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    </div>
@endif

@if($errors->any())
    <div class="container mt-3">
        <div class="alert alert-danger">
            <strong>Erreur :</strong> veuillez vérifier les informations saisies.
        </div>
    </div>
@endif

<main class="page-main">
    @yield('content')
</main>

<footer class="footer-rca">
    <div class="container text-center">
        <h5>République Centrafricaine</h5>
        <p>Plateforme Nationale d’Administration Électronique © {{ date('Y') }}</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>