<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\CitizenController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationController;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/services', [PublicController::class, 'services'])->name('services');
Route::get('/services/ministere/{ministry}', [PublicController::class, 'servicesByMinistry'])->name('services.ministry');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login']);
    Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register']);
});

Route::post('/deconnexion', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {

        Route::get('/dashboard', [AuthController::class, 'redirectDashboard'])->name('dashboard');
        Route::prefix('citoyen')->name('citizen.')->group(function () {
        Route::get('/dashboard', [CitizenController::class, 'dashboard'])->name('dashboard');
        Route::get('/demandes', [ApplicationController::class, 'index'])->name('applications');
        Route::get('/demande/nouvelle', [ApplicationController::class, 'create'])->name('application.create');
        Route::post('/demande', [ApplicationController::class, 'store'])->name('application.store');
    });

        Route::prefix('agent')->name('agent.')->group(function () {
        Route::get('/dashboard', [AgentController::class, 'dashboard'])->name('dashboard');
        Route::get('/demandes', [AgentController::class, 'applications'])->name('applications');
        Route::get('/demandes/{application}', [AgentController::class, 'show'])->name('applications.show');
        Route::match(['post', 'patch'], '/demandes/{application}/statut', [AgentController::class, 'updateStatus'])->name('applications.status');
    });

    Route::prefix('citoyen')
    ->name('citizen.')
    ->middleware(['auth', 'role:citoyen'])
    ->group(function () {
        Route::get('/dashboard', [CitizenController::class, 'dashboard'])->name('dashboard');
        Route::get('/demandes', [ApplicationController::class, 'index'])->name('applications');
        Route::get('/demande/nouvelle', [ApplicationController::class, 'create'])->name('application.create');
        Route::post('/demande', [ApplicationController::class, 'store'])->name('application.store');
    });

Route::prefix('agent')
    ->name('agent.')
    ->middleware(['auth', 'role:agent,responsable'])
    ->group(function () {
        Route::get('/dashboard', [AgentController::class, 'dashboard'])->name('dashboard');
        Route::get('/demandes', [AgentController::class, 'applications'])->name('applications');
        Route::get('/demandes/{application}', [AgentController::class, 'show'])->name('applications.show');
        Route::match(['post', 'patch'], '/demandes/{application}/statut', [AgentController::class, 'updateStatus'])->name('applications.status');
    });

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/citoyens', [AdminController::class, 'citizens'])->name('citizens.index');
        Route::get('/citoyens/{user}', [AdminController::class, 'showCitizen'])->name('citizens.show');
        Route::post('/citoyens/{user}/toggle', [AdminController::class, 'toggleUser'])->name('citizens.toggle');

        Route::get('/agents', [AdminController::class, 'agents'])->name('agents.index');
        Route::get('/agents/create', [AdminController::class, 'createAgent'])->name('agents.create');
        Route::post('/agents', [AdminController::class, 'storeAgent'])->name('agents.store');
        Route::get('/agents/{user}', [AdminController::class, 'showAgent'])->name('agents.show');
        Route::post('/agents/{user}/toggle', [AdminController::class, 'toggleUser'])->name('agents.toggle');

        Route::get('/ministeres', [AdminController::class, 'ministries'])->name('ministries.index');
        Route::get('/ministeres/create', [AdminController::class, 'createMinistry'])->name('ministries.create');
        Route::post('/ministeres', [AdminController::class, 'storeMinistry'])->name('ministries.store');
        Route::get('/ministeres/{ministry}', [AdminController::class, 'showMinistry'])->name('ministries.show');
        Route::post('/ministeres/{ministry}/toggle', [AdminController::class, 'toggleMinistry'])->name('ministries.toggle');

        Route::get('/demarches', [AdminController::class, 'procedures'])->name('procedures.index');
        Route::get('/demarches/create', [AdminController::class, 'createProcedure'])->name('procedures.create');
        Route::post('/demarches', [AdminController::class, 'storeProcedure'])->name('procedures.store');
        Route::get('/demarches/{procedure}', [AdminController::class, 'showProcedure'])->name('procedures.show');
        Route::post('/demarches/{procedure}/toggle', [AdminController::class, 'toggleProcedure'])->name('procedures.toggle');

        Route::get('/annonces', [AdminController::class, 'announcements'])->name('announcements.index');
        Route::get('/annonces/create', [AdminController::class, 'createAnnouncement'])->name('announcements.create');
        Route::post('/annonces', [AdminController::class, 'storeAnnouncement'])->name('announcements.store');
        Route::get('/annonces/{announcement}', [AdminController::class, 'showAnnouncement'])->name('announcements.show');
        Route::post('/annonces/{announcement}/toggle', [AdminController::class, 'toggleAnnouncement'])->name('announcements.toggle');

        Route::get('/parametres', [AdminController::class, 'settings'])->name('settings.index');
        Route::post('/parametres', [AdminController::class, 'updateSettings'])->name('settings.update');

        Route::get('/journal', [AdminController::class, 'auditLogs'])->name('audit.index');
    });
});