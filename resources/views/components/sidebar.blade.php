@props([
    'role',
])

<aside class="workspace-sidebar" id="workspaceSidebar">

    <div class="sidebar-header">
        <span class="sidebar-role">
            @switch($role)
                @case('admin')
                    Administration nationale
                    @break

                @case('agent')
                    Espace Agent Public
                    @break

                @case('citizen')
                    Espace Citoyen
                    @break

                @default
                    Espace sécurisé
            @endswitch
        </span>
    </div>

    <nav class="sidebar-nav">

       @if($role === 'admin')

    <a
        href="{{ route('admin.dashboard') }}"
        class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
    >
        <span class="sidebar-icon">📊</span>
        <span>Tableau de bord</span>
    </a>

    <a
        href="{{ route('admin.search.index') }}"
        class="sidebar-link {{ request()->routeIs('admin.search.*') ? 'active' : '' }}"
    >
        <span class="sidebar-icon">🔎</span>
        <span>Recherche globale</span>
    </a>

    <div class="sidebar-group-title">
        Utilisateurs
    </div>
            <a
                href="{{ route('admin.citizens.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.citizens.*') ? 'active' : '' }}"
            >
                <span class="sidebar-icon">👥</span>
                <span>Citoyens</span>
            </a>

            <a
                href="{{ route('admin.agents.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.agents.*') ? 'active' : '' }}"
            >
                <span class="sidebar-icon">🧑‍💼</span>
                <span>Agents publics</span>
            </a>

            <div class="sidebar-group-title">Organisation</div>

            <a
                href="{{ route('admin.ministries.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.ministries.*') ? 'active' : '' }}"
            >
                <span class="sidebar-icon">🏛️</span>
                <span>Ministères</span>
            </a>

            <a
                href="{{ route('admin.procedures.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.procedures.*') ? 'active' : '' }}"
            >
                <span class="sidebar-icon">📋</span>
                <span>Démarches</span>
            </a>

            <div class="sidebar-group-title">Communication</div>

            <a
                href="{{ route('admin.announcements.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}"
            >
                <span class="sidebar-icon">📢</span>
                <span>Annonces</span>
            </a>

            <div class="sidebar-group-title">Supervision</div>
			
			<a
    href="{{ route('admin.supervision.index') }}"
    class="sidebar-link {{
        request()->routeIs('admin.supervision.*')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">🛰️</span>
    <span>Supervision nationale</span>
</a>

            <a
                href="{{ route('admin.audit.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}"
            >
                <span class="sidebar-icon">🕘</span>
                <span>Journal national</span>
            </a>

            <a
                href="{{ route('admin.settings.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
            >
                <span class="sidebar-icon">⚙️</span>
                <span>Paramètres</span>
            </a>

        @elseif($role === 'agent')

            <a
                href="{{ route('agent.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('agent.dashboard') ? 'active' : '' }}"
            >
                <span class="sidebar-icon">📊</span>
                <span>Tableau de bord</span>
            </a>

            <div class="sidebar-group-title">Traitement</div>

            <a
                href="{{ route('agent.applications') }}"
                class="sidebar-link {{ request()->routeIs('agent.applications') && !request('status') ? 'active' : '' }}"
            >
                <span class="sidebar-icon">📂</span>
                <span>Toutes les demandes</span>
            </a>

            <a
                href="{{ route('agent.applications', ['status' => 'soumise']) }}"
                class="sidebar-link {{ request()->routeIs('agent.applications') && request('status') === 'soumise' ? 'active' : '' }}"
            >
                <span class="sidebar-icon">📥</span>
                <span>Nouvelles demandes</span>
            </a>

            <a
                href="{{ route('agent.applications', ['status' => 'en_traitement']) }}"
                class="sidebar-link {{ request()->routeIs('agent.applications') && request('status') === 'en_traitement' ? 'active' : '' }}"
            >
                <span class="sidebar-icon">⏳</span>
                <span>En traitement</span>
            </a>

            <a
                href="{{ route('agent.applications', ['status' => 'validee']) }}"
                class="sidebar-link {{ request()->routeIs('agent.applications') && request('status') === 'validee' ? 'active' : '' }}"
            >
                <span class="sidebar-icon">✅</span>
                <span>Validées</span>
            </a>

            <a
                href="{{ route('agent.applications', ['status' => 'rejetee']) }}"
                class="sidebar-link {{ request()->routeIs('agent.applications') && request('status') === 'rejetee' ? 'active' : '' }}"
            >
                <span class="sidebar-icon">❌</span>
                <span>Rejetées</span>
            </a>

            <a
                href="{{ route('agent.applications', ['status' => 'terminee']) }}"
                class="sidebar-link {{ request()->routeIs('agent.applications') && request('status') === 'terminee' ? 'active' : '' }}"
            >
                <span class="sidebar-icon">🏁</span>
                <span>Terminées</span>
            </a>

        @elseif($role === 'citizen')

            <a
                href="{{ route('citizen.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('citizen.dashboard') ? 'active' : '' }}"
            >
                <span class="sidebar-icon">📊</span>
                <span>Tableau de bord</span>
            </a>

            <div class="sidebar-group-title">Mes démarches</div>
			<a
              href="{{ route('citizen.payments.index') }}"
              class="sidebar-link {{ request()->routeIs('citizen.payments.*') ? 'active' : '' }}"
            >
              <span class="sidebar-icon">💳</span>
              <span>Mes paiements</span>
           </a>

            <a
                href="{{ route('citizen.application.create') }}"
                class="sidebar-link {{ request()->routeIs('citizen.application.create') ? 'active' : '' }}"
            >
                <span class="sidebar-icon">➕</span>
                <span>Nouvelle demande</span>
            </a>

            <a
                href="{{ route('citizen.applications') }}"
                class="sidebar-link {{ request()->routeIs('citizen.applications') ? 'active' : '' }}"
            >
                <span class="sidebar-icon">📁</span>
                <span>Mes demandes</span>
            </a>

            <a
                href="{{ route('services') }}"
                class="sidebar-link"
            >
                <span class="sidebar-icon">🏛️</span>
                <span>Catalogue des services</span>
            </a>

        @endif

    </nav>

</aside>