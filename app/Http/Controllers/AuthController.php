<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\AuditService;
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'citoyen',
            'active' => true,
        ]);

        Auth::login($user);

        return redirect()
            ->route('citizen.dashboard')
            ->with('success', 'Compte créé avec succès. Bienvenue sur PNAE-RCA.');
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $remember = $request->boolean('remember');

    if (!Auth::attempt($credentials, $remember)) {
        return back()
            ->withErrors([
                'email' => 'Email ou mot de passe incorrect.'
            ])
            ->onlyInput('email');
    }

    $user = Auth::user();

    if (!$user->active) {
        Auth::logout();

        return back()
            ->withErrors([
                'email' => 'Votre compte est désactivé.'
            ])
            ->onlyInput('email');
    }

    $request->session()->regenerate();

    switch ($user->role) {

        case 'admin':
            return redirect()->route('admin.dashboard');

        case 'agent':
            return redirect()->route('agent.dashboard');

        case 'responsable':
            return redirect()->route('agent.dashboard');

        case 'citoyen':
        default:
            return redirect()->route('citizen.dashboard');
    }
}

    public function redirectDashboard()
    {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'agent' => redirect()->route('agent.dashboard'),
            'responsable' => redirect()->route('agent.dashboard'),
            default => redirect()->route('citizen.dashboard'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('success', 'Vous êtes déconnecté avec succès.');
    }
}