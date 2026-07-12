@extends('layouts.citizen')

@section('title', 'Mes demandes')

@section('content')
<section class="page-section">

    <x-page-header
        title="Mes demandes"
        subtitle="Consultez vos démarches, leurs statuts et les pièces jointes déposées."
        kicker="Espace citoyen"
    >
        <x-slot:actions>
            <x-action-button
                :href="route('citizen.application.create')"
                icon="➕"
            >
                Nouvelle demande
            </x-action-button>
        </x-slot:actions>
    </x-page-header>

    <x-table-wrapper
        title="Liste des demandes"
        subtitle="Historique de vos demandes administratives."
    >
        <table class="rca-table">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Démarche</th>
                    <th>Ministère</th>
                    <th>Statut</th>
                    <th>Paiement</th>
                    <th>Date</th>
                    <th>Documents</th>
					<th>Paiement</th>
					<th>Document officiel</th>
                </tr>
            </thead>

            <tbody>
                @forelse($applications as $application)

                    <tr>
                        <td>
                            <strong>{{ $application->reference }}</strong>
                        </td>

                        <td>
                            {{ $application->procedure->title ?? 'Démarche administrative' }}
                        </td>

                        <td>
                            {{ $application->procedure->ministry->name ?? '-' }}
                        </td>

                        <td>
                            <x-status-badge
                                :status="$application->status"
                            />
                        </td>

                        <td>
                            <x-status-badge
                                :status="$application->payment_status ?? 'en_attente'"
                            />
                        </td>

                        <td>
                            {{ $application->created_at?->format('d/m/Y H:i') }}
                        </td>

                        <td>
                            @if($application->documents->isNotEmpty())
                                <span class="documents-count">
                                    {{ $application->documents->count() }}
                                    pièce(s)
                                </span>
                            @else
                                <span class="text-muted">
                                    Aucun document
                                </span>
                            @endif
                        </td>
                    </tr>

                    @if($application->documents->isNotEmpty())
                        <tr class="documents-detail-row">
                            <td colspan="7">

                                <div class="citizen-documents-block">

                                    <div class="citizen-documents-heading">
                                        <h3>Pièces jointes</h3>

                                        <span>
                                            Demande {{ $application->reference }}
                                        </span>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="rca-table citizen-documents-table">
                                            <thead>
                                                <tr>
                                                    <th>Nom du fichier</th>
                                                    <th>Type</th>
                                                    <th>Taille</th>
                                                    <th>Contrôle</th>
                                                    <th>Observation</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach($application->documents as $document)
                                                    <tr>
                                                        <td>
                                                            <strong>
                                                                {{ $document->display_name }}
                                                            </strong>
                                                        </td>

                                                        <td>
                                                            {{ $document->mime_type ?? '-' }}
                                                        </td>

                                                        <td>
                                                            {{ $document->formatted_size }}
                                                        </td>

                                                        <td>
                                                            <x-status-badge
                                                                :status="$document->status"
                                                                :label="$document->status_label"
                                                            />
                                                        </td>
														
														
	<td>
    @if($application->officialDocument)

        <x-action-button
            :href="route(
                'citizen.official-documents.download',
                $application->officialDocument
            )"
            size="small"
            variant="success"
            icon="⬇️"
        >
            Télécharger
        </x-action-button>

    @elseif(
        in_array(
            $application->status,
            ['validee', 'terminee'],
            true
        )
    )
        <span class="text-muted">
            En préparation
        </span>
    @else
        <span class="text-muted">
            Non disponible
        </span>
    @endif
</td>



<td>
    @if((float) ($application->procedure->fee ?? 0) > 0
        && $application->payment_status !== 'paye'
    )
        <x-action-button
            :href="route('citizen.payments.create', $application)"
            size="small"
            variant="warning"
            icon="💳"
        >
            Payer
        </x-action-button>
    @elseif($application->payment_status === 'paye')
        <x-status-badge status="paye" />
    @else
        <span class="text-muted">Gratuit</span>
    @endif
</td>


                                                        <td>
                                                            @if($document->note)
                                                                <details class="citizen-document-note">
                                                                    <summary>
                                                                        Voir l’observation
                                                                    </summary>

                                                                    <p>
                                                                        {{ $document->note }}
                                                                    </p>
                                                                </details>
                                                            @else
                                                                <span class="text-muted">
                                                                    Aucune observation
                                                                </span>
                                                            @endif
                                                        </td>

                                                        <td>
                                                            <x-action-button
                                                                :href="asset('storage/' . $document->file_path)"
                                                                target="_blank"
                                                                variant="warning"
                                                                size="small"
                                                                icon="⬇️"
                                                            >
                                                                Télécharger
                                                            </x-action-button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                            </td>
                        </tr>
                    @endif

                @empty

                    <tr>
                        <td colspan="7">
                            <x-empty-state
                                icon="📭"
                                title="Aucune demande"
                                message="Vous n’avez encore déposé aucune demande administrative."
                            >
                                <x-slot:action>
                                    <x-action-button
                                        :href="route('citizen.application.create')"
                                        icon="➕"
                                    >
                                        Déposer une demande
                                    </x-action-button>
                                </x-slot:action>
                            </x-empty-state>
                        </td>
                    </tr>

                @endforelse
            </tbody>
        </table>
    </x-table-wrapper>

</section>
@endsection