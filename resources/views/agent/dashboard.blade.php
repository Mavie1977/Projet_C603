@extends('layouts.app')

@section('title', 'Tableau de bord agent')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Tableau de bord Agent Public</h1>
        <p>Suivi et traitement des demandes administratives.</p>
    </div>

    <div class="dashboard-grid">
        <div class="stat-card"><span>Total dossiers</span><strong>{{ $total }}</strong></div>
        <div class="stat-card"><span>Soumises</span><strong>{{ $soumise }}</strong></div>
        <div class="stat-card"><span>En traitement</span><strong>{{ $traitement }}</strong></div>
        <div class="stat-card"><span>Validées</span><strong>{{ $validee }}</strong></div>
    </div>

    <div class="table-card mt-4">
        <div class="card-title-row mb-3">
            <h2>Demandes récentes</h2>
            <a href="{{ route('agent.applications') }}" class="btn-rca-primary">
                Voir toutes les demandes
            </a>
        </div>

        <table class="rca-table">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Citoyen</th>
                    <th>Démarche</th>
                    <th>Statut</th>
                    <th>Documents</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $application)
                    <tr>
                        <td><strong>{{ $application->reference }}</strong></td>
                        <td>{{ $application->user->name ?? '-' }}</td>
                        <td>{{ $application->procedure->title ?? '-' }}</td>
                        <td>
                            <span class="badge-status {{ $application->status }}">
                                {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                            </span>
                        </td>
                        <td>{{ $application->documents->count() }} pièce(s)</td>
                        <td>
                            <a href="{{ route('agent.applications.show', $application) }}" class="btn-table">
                                Traiter
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Aucun dossier disponible.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</section>
@endsection