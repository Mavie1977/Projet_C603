<?php

namespace App\Http\Controllers;

use App\Models\OfficialDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    public function show(
        Request $request,
        OfficialDocument $officialDocument
    ) {
        abort_unless(
            hash_equals(
                $officialDocument->verification_token,
                (string) $request->query('token')
            ),
            403,
            'Jeton de vérification invalide.'
        );

        $officialDocument->load([
            'application.user',
            'application.procedure.ministry',
            'generator',
        ]);

        $fileExists = Storage::disk('local')->exists(
            $officialDocument->file_path
        );

        $currentHash = $fileExists
            ? hash(
                'sha256',
                Storage::disk('local')->get(
                    $officialDocument->file_path
                )
            )
            : null;

        $hashValid = $fileExists
            && hash_equals(
                $officialDocument->hash_sha256,
                $currentHash
            );

        return view(
            'public.verification.show',
            compact(
                'officialDocument',
                'fileExists',
                'hashValid'
            )
        );
    }
}