@extends('layouts.app')

@section('title', 'Traitement dossier')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Traitement du dossier</h1>
        <p>{{ $application->reference }}</p>
    </div>

    <div class="dashboard-card mb-4">
        <h2>Informations du dossier</h2>

        <table class="rca-table mt-3">
            <tr><th>Référence</th><td>{{ $application->reference }}</td></tr>
            <tr><th>Citoyen</th><td>{{ $application->user->name ?? '-' }}</td></tr>
            <tr><th>Email</th><td>{{ $application->user->email ?? '-' }}</td></tr>
            <tr><th>Démarche</th><td>{{ $application->procedure->title ?? '-' }}</td></tr>
            <tr><th>Ministère</th><td>{{ $application->procedure->ministry->name ?? '-' }}</td></tr>
            <tr><th>Paiement</th><td>{{ str_replace('_', ' ', $application->payment_status ?? 'en_attente') }}</td></tr>
            <tr>
                <th>Statut actuel</th>
                <td>
                    <span class="badge-status {{ $application->status }}">
                        {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                    </span>
                </td>
            </tr>
            <tr><th>Message citoyen</th><td>{{ $application->message ?? '-' }}</td></tr>
        </table>
    </div>

    <div class="table-card mb-4">
        <h2>Pièces jointes</h2>

        <table class="rca-table mt-3">
            <thead>
                <tr>
                    <th>Nom du fichier</th>
                    <th>Type</th>
                    <th>Taille</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($application->documents as $document)
                    <tr>
                        <td>{{ $document->original_name ?? $document->label }}</td>
                        <td>{{ $document->mime_type ?? '-' }}</td>
                        <td>{{ $document->size ? round($document->size / 1024, 1) . ' Ko' : '-' }}</td>
                        <td>
                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="btn-table">
                                Télécharger
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Aucune pièce jointe.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="form-card mb-4">
        <div class="form-header">
            <h2>Changer le statut</h2>
            <p>Mettre le dossier en traitement, le valider ou le rejeter.</p>
        </div>

        <form method="POST" action="{{ route('agent.applications.status', $application) }}">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label>Nouveau statut</label>
                    <select name="status" required>
                        <option value="en_traitement">En traitement</option>
                        <option value="validee">Validée</option>
                        <option value="rejetee">Rejetée</option>
                        <option value="terminee">Terminée</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Commentaire agent</label>
                    <textarea name="comment" rows="4" placeholder="Ajouter un commentaire..."></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-rca-primary">Enregistrer le statut</button>
                <a href="{{ route('agent.applications') }}" class="btn-rca-secondary">Retour</a>
            </div>
        </form>
    </div>

    <div class="table-card">
        <h2>Historique du traitement</h2>

        <table class="rca-table mt-3">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Agent</th>
                    <th>Ancien statut</th>
                    <th>Nouveau statut</th>
                    <th>Commentaire</th>
                </tr>
            </thead>

            <tbody>
                @forelse($application->workflowLogs->sortByDesc('created_at') as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->user->name ?? 'Système' }}</td>
                        <td>{{ str_replace('_', ' ', $log->from_status ?? '-') }}</td>
                        <td>{{ str_replace('_', ' ', $log->to_status ?? '-') }}</td>
                        <td>{{ $log->comment ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Aucun historique pour ce dossier.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</section>
@endsection