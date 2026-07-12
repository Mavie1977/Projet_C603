<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\OfficialDocument;
use App\Services\OfficialDocumentService;
use Illuminate\Support\Facades\Storage;

class OfficialDocumentController extends Controller
{
    public function store(
        Application $application,
        OfficialDocumentService $officialDocumentService
    ) {
        $officialDocumentService->generate($application);

        return redirect()
            ->route('agent.applications.show', $application)
            ->with(
                'success',
                'Le document officiel a été généré avec succès.'
            );
    }

    public function download(
        OfficialDocument $officialDocument
    ) {
        abort_unless(
            Storage::disk('local')->exists(
                $officialDocument->file_path
            ),
            404,
            'Le fichier officiel est introuvable.'
        );

        return Storage::disk('local')->download(
            $officialDocument->file_path,
            $officialDocument->official_number . '.pdf',
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }
}