<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ministry;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MinistryController extends Controller
{
    public function index()
    {
        $ministries = Ministry::withCount('procedures')
            ->orderBy('name')
            ->get();

        return view('admin.ministries.index', compact('ministries'));
    }

    public function create()
    {
        return view('admin.ministries.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                'unique:ministries,name',
            ],
            'description' => ['nullable', 'string'],
        ]);

        $ministry = Ministry::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'active' => true,
        ]);

        AuditService::log(
            'Création',
            'Ministère',
            $ministry->id,
            ['name' => $ministry->name]
        );

        return redirect()
            ->route('admin.ministries.index')
            ->with('success', 'Ministère créé avec succès.');
    }

    public function show(Ministry $ministry)
    {
        $ministry->load('procedures');

        return view(
            'admin.ministries.show',
            compact('ministry')
        );
    }

    public function toggle(Ministry $ministry)
    {
        $ministry->update([
            'active' => ! $ministry->active,
        ]);

        AuditService::log(
            'Changement de statut',
            'Ministère',
            $ministry->id,
            [
                'name' => $ministry->name,
                'active' => $ministry->active,
            ]
        );

        return back()->with(
            'success',
            'Statut du ministère modifié avec succès.'
        );
    }
}