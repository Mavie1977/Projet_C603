<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>@yield('title', 'Administration nationale — PNAE-RCA')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="admin-layout">

<header class="main-header admin-header">

    <div class="brand-block">
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <div class="rca-flag" aria-hidden="true">
                <span class="flag-star">★</span>
            </div>

            <div>
                <strong>PNAE-RCA</strong>
                <small>Administration nationale</small>
            </div>
        </a>
    </div>

    <nav class="main-nav">
        <a href="{{ route('home') }}">Portail public</a>

        <x-user-account
            dashboard-route="admin.dashboard"
            role-label="Administrateur"
        />
    </nav>

</header>

<div class="workspace-shell">

    <x-sidebar role="admin" />

    <div class="workspace-content">

        <button
            type="button"
            class="sidebar-toggle"
            onclick="toggleWorkspaceSidebar()"
            aria-label="Afficher ou masquer le menu"
        >
            ☰ Menu
        </button>

        <main class="workspace-main">

           <div class="layout-alerts">

    @if(session('success'))
        <x-alert type="success" dismissible>
            {{ session('success') }}
        </x-alert>
    @endif

    @if(session('warning'))
        <x-alert type="warning" dismissible>
            {{ session('warning') }}
        </x-alert>
    @endif

    @if(session('error'))
        <x-alert type="error" dismissible>
            {{ session('error') }}
        </x-alert>
    @endif

</div>
            

            @yield('content')

        </main>

        <footer class="site-footer">
            <strong>République Centrafricaine</strong>

            <p>
                Administration nationale — PNAE-RCA © {{ date('Y') }}
            </p>
        </footer>

    </div>

</div>

<div
    class="sidebar-overlay"
    id="sidebarOverlay"
    onclick="closeWorkspaceSidebar()"
></div>

<script>
    function toggleWorkspaceSidebar() {
        const sidebar = document.getElementById('workspaceSidebar');
        const overlay = document.getElementById('sidebarOverlay');

        sidebar?.classList.toggle('sidebar-open');
        overlay?.classList.toggle('sidebar-overlay-visible');
    }

    function closeWorkspaceSidebar() {
        document
            .getElementById('workspaceSidebar')
            ?.classList
            .remove('sidebar-open');

        document
            .getElementById('sidebarOverlay')
            ?.classList
            .remove('sidebar-overlay-visible');
    }
</script>

@stack('scripts')
</body>
</html>