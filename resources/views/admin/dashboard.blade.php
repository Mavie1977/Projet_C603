@extends('layouts.app')

@section('title', 'Tableau de bord administrateur')

@section('content')

<section class="page-section">

    <div class="page-heading">
        <h1>Tableau de bord national</h1>
        <p>Vue globale de la Plateforme Nationale d’Administration Électronique.</p>
    </div>

    <div class="dashboard-grid mb-4">
        <div class="stat-card">
            <span>Citoyens</span>
            <strong>{{ $stats['citizens'] }}</strong>
        </div>

        <div class="stat-card">
            <span>Agents</span>
            <strong>{{ $stats['agents'] }}</strong>
        </div>

        <div class="stat-card">
            <span>Ministères</span>
            <strong>{{ $stats['ministries'] }}</strong>
        </div>

        <div class="stat-card">
            <span>Démarches</span>
            <strong>{{ $stats['procedures'] }}</strong>
        </div>
    </div>

    <div class="dashboard-grid mb-4">
        <div class="stat-card">
            <span>Total dossiers</span>
            <strong>{{ $stats['applications'] }}</strong>
        </div>

        <div class="stat-card">
            <span>Soumises</span>
            <strong>{{ $stats['submitted'] }}</strong>
        </div>

        <div class="stat-card">
            <span>En traitement</span>
            <strong>{{ $stats['processing'] }}</strong>
        </div>

        <div class="stat-card">
            <span>Validées</span>
            <strong>{{ $stats['validated'] }}</strong>
        </div>
    </div>

    <div class="table-card mb-4">
        <div class="card-title-row mb-3">
            <h2>Derniers dossiers</h2>
            <a href="{{ route('agent.applications') }}" class="btn-rca-primary">
                Voir les dossiers
            </a>
        </div>

        <table class="rca-table">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Citoyen</th>
                    <th>Démarche</th>
                    <th>Ministère</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                @forelse($latestApplications as $application)
                    <tr>
                        <td><strong>{{ $application->reference }}</strong></td>
                        <td>{{ $application->user->name ?? '-' }}</td>
                        <td>{{ $application->procedure->title ?? '-' }}</td>
                        <td>{{ $application->procedure->ministry->name ?? '-' }}</td>
                        <td>
                            <span class="badge-status {{ $application->status }}">
                                {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                            </span>
                        </td>
                        <td>{{ $application->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Aucun dossier enregistré.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-card mb-4">
        <div class="card-title-row mb-3">
            <h2>Utilisateurs récents</h2>
        </div>

        <table class="rca-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Date création</th>
                </tr>
            </thead>

            <tbody>
                @forelse($latestUsers as $user)
                    <tr>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>
                            @if($user->active)
                                <span class="badge-status validee">Actif</span>
                            @else
                                <span class="badge-status rejetee">Inactif</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Aucun utilisateur enregistré.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="dashboard-card">
        <h2>Accès rapides administrateur</h2>

        <div class="quick-actions mt-3">
            <a href="{{ route('admin.citizens.index') }}" class="quick-action-card">
                   <strong>Utilisateurs</strong>
                   <span>Gérer les comptes citoyens</span>
            </a>

            <a href="{{ route('admin.agents.index') }}" class="quick-action-card">
                    <strong>Agents publics</strong>
                    <span>Créer et gérer les agents</span>
           </a>

            <a href="{{ route('admin.ministries.index') }}" class="quick-action-card">
                   <strong>Ministères</strong>
                   <span>Organiser les ministères</span>
           </a>

            <a href="#" class="quick-action-card">
                <strong>Démarches</strong>
                <span>Configurer les services</span>
            </a>
			
			<a href="{{ route('admin.announcements.index') }}" class="quick-action-card">
                  <strong>Annonces</strong>
                 <span>Publier les communiqués nationaux</span>
           </a>
		   <a href="{{ route('admin.settings.index') }}"
                 class="quick-action-card">
                 <strong>Paramètres</strong>
                <span>Configurer le portail national</span>
         </a>
        </div>
    </div>

</section>

@endsection