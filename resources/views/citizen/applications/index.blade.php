@extends('layouts.app')

@section('title', 'Mes demandes')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Mes demandes</h1>
        <p>Consultez vos démarches, leurs statuts et les pièces jointes déposées.</p>
    </div>

    <div class="table-card">

        <div class="card-title-row mb-3">
            <h2>Liste des demandes</h2>
            <a href="{{ route('citizen.application.create') }}" class="btn-rca-primary">
                Nouvelle demande
            </a>
        </div>

        <table class="rca-table">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Démarche</th>
                    <th>Statut</th>
                    <th>Paiement</th>
                    <th>Date</th>
                    <th>Documents</th>
                </tr>
            </thead>

            <tbody>
                @forelse($applications as $application)
                    <tr>
                        <td><strong>{{ $application->reference }}</strong></td>
                        <td>{{ $application->procedure->title ?? 'Démarche administrative' }}</td>
                        <td>
                            <span class="badge-status {{ $application->status }}">
                                {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                            </span>
                        </td>
                        <td>{{ str_replace('_', ' ', $application->payment_status ?? 'en_attente') }}</td>
                        <td>{{ $application->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if($application->documents->count() > 0)
                                <button type="button" class="btn-table" data-bs-toggle="modal" data-bs-target="#docsModal{{ $application->id }}">
                                    Voir documents
                                </button>
                            @else
                                <span class="text-muted">Aucun document</span>
                            @endif
                        </td>
                    </tr>

                    @if($application->documents->count() > 0)
                        <div class="modal fade" id="docsModal{{ $application->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">

                                    <div class="modal-header modal-rca-header">
                                        <h5 class="modal-title">
                                            Pièces jointes - {{ $application->reference }}
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <table class="rca-table">
                                            <thead>
                                                <tr>
                                                    <th>Nom du fichier</th>
                                                    <th>Type</th>
                                                    <th>Taille</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach($application->documents as $document)
                                                    <tr>
                                                        <td>{{ $document->original_name }}</td>
                                                        <td>{{ $document->mime_type }}</td>
                                                        <td>{{ round($document->size / 1024, 1) }} Ko</td>
                                                        <td>
                                                            <a href="{{ asset('storage/' . $document->file_path)}}" target="_blank" class="btn-table">
                                                                Télécharger
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endif

                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            Aucune demande enregistrée.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

</section>
@endsection