<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ministry;
use App\Models\Procedure;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProcedureController extends Controller
{
    public function index()
    {
        $procedures = Procedure::with('ministry')
            ->orderBy('title')
            ->get();

        return view('admin.procedures.index', compact('procedures'));
    }

    public function create()
    {
        $ministries = Ministry::where('active', true)
            ->orderBy('name')
            ->get();

        return view(
            'admin.procedures.create',
            compact('ministries')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ministry_id' => [
                'required',
                'exists:ministries,id',
            ],
            'title' => [
                'required',
                'string',
                'max:150',
                'unique:procedures,title',
            ],
            'description' => ['nullable', 'string'],
            'required_documents' => ['nullable', 'string'],
            'fee' => ['nullable', 'numeric', 'min:0'],
            'processing_days' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ]);

        $procedure = Procedure::create([
            'ministry_id' => $validated['ministry_id'],
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'] ?? null,
            'required_documents' =>
                $validated['required_documents'] ?? null,
            'fee' => $validated['fee'] ?? 0,
            'processing_days' =>
                $validated['processing_days'] ?? 7,
            'active' => true,
        ]);

        AuditService::log(
            'Création',
            'Démarche',
            $procedure->id,
            [
                'title' => $procedure->title,
                'ministry_id' => $procedure->ministry_id,
            ]
        );

        return redirect()
            ->route('admin.procedures.index')
            ->with('success', 'Démarche créée avec succès.');
    }

    public function show(Procedure $procedure)
    {
        $procedure->load('ministry');

        return view(
            'admin.procedures.show',
            compact('procedure')
        );
    }

    public function toggle(Procedure $procedure)
    {
        $procedure->update([
            'active' => ! $procedure->active,
        ]);

        AuditService::log(
            'Changement de statut',
            'Démarche',
            $procedure->id,
            [
                'title' => $procedure->title,
                'active' => $procedure->active,
            ]
        );

        return back()->with(
            'success',
            'Statut de la démarche modifié avec succès.'
        );
    }
}