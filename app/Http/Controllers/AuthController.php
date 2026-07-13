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

       $user = User::create([
           'name' => $validated['name'],
           'email' => $validated['email'],
           'phone' => $validated['phone'] ?? null,
           'password' => $validated['password'],
           'role' => User::ROLE_CITOYEN,
           'active' => true,
           'ministry_id' => null,
      ]);

        Auth::login($user);

        return redirect()
            ->route('citizen.dashboard')
            ->with('success', 'Compte créé avec succès. Bienvenue sur PNAE-RCA.');
    }

    public function login(Request $request)
{
    $validated = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $credentials = [
        'email' => $validated['email'],
        'password' => $validated['password'],
        'active' => true,
    ];

    if (! auth()->attempt(
        $credentials,
        $request->boolean('remember')
    )) {
        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' =>
                    'Adresse email, mot de passe incorrect ou compte désactivé.',
            ]);
    }

    $request->session()->regenerate();

    return $this->redirectAuthenticatedUser();
}

public function redirectDashboard()
{
    return $this->redirectAuthenticatedUser();
}

private function redirectAuthenticatedUser()
{
    return match (auth()->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),

        'agent',
        'responsable' => redirect()->route('agent.dashboard'),

        'citoyen' => redirect()->route('citizen.dashboard'),

        default => redirect()->route('home')
            ->with('warning', 'Rôle utilisateur non reconnu.'),
    };
}
public function logout(Request $request)
{
    auth()->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()
        ->route('home')
        ->with('success', 'Vous êtes déconnecté avec succès.');
}
}