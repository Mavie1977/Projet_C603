<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AuditService;

class AgentController extends Controller
{
    public function dashboard()
    {
        $applications = Application::with(['user', 'procedure', 'documents'])
            ->latest()
            ->get();

        return view('agent.dashboard', [
            'total' => $applications->count(),
            'soumise' => $applications->where('status', 'soumise')->count(),
            'traitement' => $applications->where('status', 'en_traitement')->count(),
            'validee' => $applications->where('status', 'validee')->count(),
            'rejetee' => $applications->where('status', 'rejetee')->count(),
            'applications' => $applications->take(8),
        ]);
    }

    public function applications()
    {
        $applications = Application::with(['user', 'procedure', 'documents'])
            ->latest()
            ->get();

        return view('agent.applications.index', compact('applications'));
    }

    public function show(Application $application)
    {
        $application->load([
            'user',
            'procedure.ministry',
            'documents',
            'workflowLogs.user',
        ]);

        return view('agent.applications.show', compact('application'));
    }

    public function updateStatus(Request $request, Application $application)
    {
        $request->validate([
            'status' => ['required', 'in:soumise,en_traitement,validee,rejetee,terminee'],
            'comment' => ['nullable', 'string'],
        ]);

        $oldStatus = $application->status;

        $application->update([
            'status' => $request->status,
        ]);

        DB::table('workflow_logs')->insert([
            'application_id' => $application->id,
            'user_id' => auth()->id(),
            'from_status' => $oldStatus,
            'to_status' => $request->status,
            'comment' => $request->comment,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('agent.applications.show', $application)
            ->with('success', 'Statut du dossier mis à jour avec succès.');
    }
}