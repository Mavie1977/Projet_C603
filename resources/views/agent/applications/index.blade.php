@extends('layouts.app')

@section('title', 'Demandes agent')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Demandes administratives</h1>
        <p>Liste complète des dossiers déposés par les citoyens.</p>
    </div>

    <div class="table-card">
        <table class="rca-table">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Citoyen</th>
                    <th>Démarche</th>
                    <th>Statut</th>
                    <th>Paiement</th>
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
                        <td>{{ str_replace('_', ' ', $application->payment_status ?? 'en_attente') }}</td>
                        <td>{{ $application->documents->count() }} pièce(s)</td>
                        <td>
                            <a href="{{ route('agent.applications.show', $application) }}" class="btn-table">
                                Ouvrir
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Aucune demande à traiter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</section>
@endsection