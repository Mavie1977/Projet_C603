<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Admin\NationalDashboardController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\NationalReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CitizenController as CitizenDashboardController;
use App\Http\Controllers\Citizen\ApplicationController as CitizenApplicationController;

use App\Http\Controllers\Agent\DashboardController as AgentDashboardController;
use App\Http\Controllers\Agent\ApplicationController as AgentApplicationController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CitizenController as AdminCitizenController;
use App\Http\Controllers\Admin\AgentController as AdminAgentController;
use App\Http\Controllers\Admin\MinistryController as AdminMinistryController;
use App\Http\Controllers\Admin\ProcedureController as AdminProcedureController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\AuditController as AdminAuditController;
use App\Http\Controllers\Admin\SearchController as AdminSearchController;
use App\Http\Controllers\Agent\DocumentController as AgentDocumentController;
use App\Http\Controllers\Citizen\PaymentController as CitizenPaymentController;
use App\Http\Controllers\Agent\OfficialDocumentController as AgentOfficialDocumentController;
use App\Http\Controllers\Citizen\OfficialDocumentController as CitizenOfficialDocumentController;
use App\Http\Controllers\VerificationController;



Route::get(
    '/verification/document/{officialDocument}',
    [VerificationController::class, 'show']
)
    ->middleware('signed')
    ->name('verification.documents.show');
	Route::get(
    '/verification',
    [VerificationController::class, 'index']
)->name('verification.index');

Route::post(
    '/verification',
    [VerificationController::class, 'search']
)->name('verification.search');

Route::get(
    '/verification/resultat/{officialDocument}',
    [VerificationController::class, 'publicShow']
)->name('verification.documents.public');

Route::get(
    '/verification/document/{officialDocument}',
    [VerificationController::class, 'show']
)
    ->middleware('signed')
    ->name('verification.documents.show');
	
/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/services', [PublicController::class, 'services'])->name('services');

Route::get(
    '/services/ministere/{ministry}',
    [PublicController::class, 'servicesByMinistry']
)->name('services.ministry');

Route::get('/contact', [PublicController::class, 'contact'])
    ->name('contact');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/connexion', [AuthController::class, 'login']);

    Route::get('/inscription', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/inscription', [AuthController::class, 'register']);
});

Route::post('/deconnexion', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/dashboard', [AuthController::class, 'redirectDashboard'])
    ->middleware('auth')
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Citizen
|--------------------------------------------------------------------------
*/

Route::prefix('citoyen')
    ->name('citizen.')
    ->middleware(['auth', 'role:citoyen'])
    ->group(function () {
        Route::get(
            '/dashboard',
            [CitizenDashboardController::class, 'dashboard']
        )->name('dashboard');

        Route::get(
            '/demandes',
            [CitizenApplicationController::class, 'index']
        )->name('applications');

        Route::get(
            '/demande/nouvelle',
            [CitizenApplicationController::class, 'create']
        )->name('application.create');

        Route::post(
            '/demande',
            [CitizenApplicationController::class, 'store']
        )->name('application.store');
		Route::get(
            '/paiements',
            [CitizenPaymentController::class, 'index']
        )->name('payments.index');

        Route::get(
            '/demandes/{application}/paiement',
            [CitizenPaymentController::class, 'create']
            )->name('payments.create');

       Route::post(
           '/demandes/{application}/paiement',
           [CitizenPaymentController::class, 'store']
           )->name('payments.store');

      Route::get(
          '/paiements/{payment}',
          [CitizenPaymentController::class, 'show']
          )->name('payments.show');

     Route::post(
         '/paiements/{payment}/confirmer',
         [CitizenPaymentController::class, 'confirm']
         )->name('payments.confirm');

     Route::post(
         '/paiements/{payment}/annuler',
         [CitizenPaymentController::class, 'cancel']
         )->name('payments.cancel');
		 Route::get(
             '/documents-officiels/{officialDocument}/telecharger',
             [CitizenOfficialDocumentController::class, 'download']
             )->name('official-documents.download');
			 
			 Route::prefix('citoyen')
                   ->name('citizen.')
                   ->middleware(['auth', 'role:citoyen'])
                   ->group(function () {

        Route::get(
            '/documents-officiels/{officialDocument}/telecharger',
            [CitizenOfficialDocumentController::class, 'download']
        )->name('official-documents.download');
    
    });
	});

/*
|--------------------------------------------------------------------------
| Agent
|--------------------------------------------------------------------------
*/

Route::prefix('agent')
    ->name('agent.')
    ->middleware(['auth', 'role:agent,responsable'])
    ->group(function () {
	
        Route::get(
            '/dashboard',
            [AgentDashboardController::class, 'index']
            )->name('dashboard');
			
		Route::get(
            '/documents/{document}/voir',
            [AgentDocumentController::class, 'show']
            )->name('documents.show');

      Route::get(
          '/documents/{document}/telecharger',
          [AgentDocumentController::class, 'download']
          )->name('documents.download');

     Route::patch(
         '/documents/{document}/statut',
         [AgentDocumentController::class, 'updateStatus']
         )->name('documents.status');

        Route::get(
            '/demandes',
            [AgentApplicationController::class, 'index']
        )->name('applications');

        Route::get(
            '/demandes/{application}',
            [AgentApplicationController::class, 'show']
        )->name('applications.show');

        Route::match(
            ['post', 'patch'],
            '/demandes/{application}/statut',
            [AgentApplicationController::class, 'updateStatus']
        )->name('applications.status');
		Route::post(
            '/demandes/{application}/document-officiel',
            [AgentOfficialDocumentController::class, 'store']
            )->name('official-documents.store');

       Route::get(
           '/documents-officiels/{officialDocument}/telecharger',
           [AgentOfficialDocumentController::class, 'download']
           )->name('official-documents.download');

      Route::prefix('agent')
           ->name('agent.')
           ->middleware(['auth', 'role:agent,responsable'])
           ->group(function () {

        Route::get(
            '/documents-officiels/{officialDocument}/telecharger',
            [AgentOfficialDocumentController::class, 'download']
        )->name('official-documents.download');
    });
});
/*
|--------------------------------------------------------------------------
| Administration
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        Route::get(
            '/dashboard',
            [AdminDashboardController::class, 'index']
        )->name('dashboard');

        Route::get(
            '/recherche',
            [SearchController::class, 'index']
        )->name('search.index');

        Route::get(
            '/journal',
            [AdminAuditController::class, 'index']
        )->name('audit.index');

        Route::get(
            '/supervision',
            [NationalDashboardController::class, 'index']
        )->name('supervision.index');


       Route::get(
        '/supervision/data',
        [NationalDashboardController::class, 'data']
       )->name('supervision.data');
	   
	   Route::get(
        '/supervision/rapport/pdf',
        [NationalReportController::class, 'pdf']
        )->name('supervision.report.pdf');

     Route::get(
        '/supervision/rapport/excel',
        [NationalReportController::class, 'excel']
        )->name('supervision.report.excel');
		
    Route::resource('utilisateurs', UserController::class)
    ->parameters([
        'utilisateurs' => 'user',
    ])
    ->names('users');

    Route::patch(
    '/utilisateurs/{user}/activation',
    [UserController::class, 'toggle']
   )->name('users.toggle');

   Route::post(
    '/utilisateurs/{user}/mot-de-passe',
    [UserController::class, 'resetPassword']
    )->name('users.reset-password');

    });
	

		Route::get(
            '/recherche',
            [AdminSearchController::class, 'index']
       )->name('search.index');
	   
	   Route::prefix('admin')
              ->name('admin.')
              ->middleware(['auth', 'role:admin'])
              ->group(function () {
	   
	   Route::get(
           '/demandes/{application}',
            [AdminSearchController::class, 'showApplication']
      )->name('applications.show');

        Route::get(
            '/citoyens',
            [AdminCitizenController::class, 'index']
        )->name('citizens.index');

        Route::get(
            '/citoyens/{user}',
            [AdminCitizenController::class, 'show']
        )->name('citizens.show');

        Route::post(
            '/citoyens/{user}/toggle',
            [AdminCitizenController::class, 'toggle']
        )->name('citizens.toggle');

        Route::get(
            '/agents',
            [AdminAgentController::class, 'index']
        )->name('agents.index');

        Route::get(
            '/agents/create',
            [AdminAgentController::class, 'create']
        )->name('agents.create');

        Route::post(
            '/agents',
            [AdminAgentController::class, 'store']
        )->name('agents.store');

        Route::get(
            '/agents/{user}',
            [AdminAgentController::class, 'show']
        )->name('agents.show');

        Route::post(
            '/agents/{user}/toggle',
            [AdminAgentController::class, 'toggle']
        )->name('agents.toggle');

        Route::get(
            '/ministeres',
            [AdminMinistryController::class, 'index']
        )->name('ministries.index');

        Route::get(
            '/ministeres/create',
            [AdminMinistryController::class, 'create']
        )->name('ministries.create');

        Route::post(
            '/ministeres',
            [AdminMinistryController::class, 'store']
        )->name('ministries.store');

        Route::get(
            '/ministeres/{ministry}',
            [AdminMinistryController::class, 'show']
        )->name('ministries.show');

        Route::post(
            '/ministeres/{ministry}/toggle',
            [AdminMinistryController::class, 'toggle']
        )->name('ministries.toggle');

        Route::get(
            '/demarches',
            [AdminProcedureController::class, 'index']
        )->name('procedures.index');

        Route::get(
            '/demarches/create',
            [AdminProcedureController::class, 'create']
        )->name('procedures.create');

        Route::post(
            '/demarches',
            [AdminProcedureController::class, 'store']
        )->name('procedures.store');

        Route::get(
            '/demarches/{procedure}',
            [AdminProcedureController::class, 'show']
        )->name('procedures.show');

        Route::post(
            '/demarches/{procedure}/toggle',
            [AdminProcedureController::class, 'toggle']
        )->name('procedures.toggle');

        Route::get(
            '/annonces',
            [AdminAnnouncementController::class, 'index']
        )->name('announcements.index');

        Route::get(
            '/annonces/create',
            [AdminAnnouncementController::class, 'create']
        )->name('announcements.create');

        Route::post(
            '/annonces',
            [AdminAnnouncementController::class, 'store']
        )->name('announcements.store');

        Route::get(
            '/annonces/{announcement}',
            [AdminAnnouncementController::class, 'show']
        )->name('announcements.show');

        Route::post(
            '/annonces/{announcement}/toggle',
            [AdminAnnouncementController::class, 'toggle']
        )->name('announcements.toggle');

        Route::get(
            '/parametres',
            [AdminSettingController::class, 'index']
        )->name('settings.index');

        Route::post(
            '/parametres',
            [AdminSettingController::class, 'update']
        )->name('settings.update');

		
    });