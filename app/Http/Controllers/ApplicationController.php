<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Procedure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\AuditService;
class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::with(['procedure', 'documents'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('citizen.applications.index', compact('applications'));
    }

    public function create()
    {
        $procedures = Procedure::with('ministry')
            ->where('active', true)
            ->orderBy('title')
            ->get();

        return view('citizen.applications.create', compact('procedures'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'procedure_id' => ['required', 'exists:procedures,id'],
            'priority' => ['nullable', 'string', 'max:50'],
            'message' => ['nullable', 'string'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $application = Application::create([
            'user_id' => auth()->id(),
            'procedure_id' => $request->procedure_id,
            'reference' => 'PNAE-' . date('Y') . '-' . strtoupper(Str::random(8)),
            'status' => 'soumise',
            'payment_status' => 'en_attente',
            'priority' => $request->priority ?? 'normale',
            'message' => $request->message,
        ]);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('documents/applications', 'public');

                DB::table('application_documents')->insert([
    'application_id' => $application->id,
    'label' => $file->getClientOriginalName(),
    'file_path' => $path,
    'status' => 'attendu',
    'note' => null,
    'original_name' => $file->getClientOriginalName(),
    'mime_type' => $file->getMimeType(),
    'size' => $file->getSize(),
    'created_at' => now(),
    'updated_at' => now(),
]);
            }
        }

        return redirect()
            ->route('citizen.applications')
            ->with('success', 'Votre demande a été déposée avec succès.');
    }
}