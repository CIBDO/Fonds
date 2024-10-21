<?php
use App\Http\Controllers\{
    ProfileController,
    MessageController,
    DemandeFondsController,
    RapportPaiementController,
    ReceptionFondsController,
    UserController,
    AttachmentController,
    AdminController,
    TresorierController,
    AcctController,
    PosteController,
    EnvoisFondsController,
    SuperviseurController,
};
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Route pour la page d'accueil (connexion)
Route::get('/', fn() => view('auth.login'))->name('custom.login');

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    // Dashboard spécifique à chaque rôle
    Route::get('/dashboard', function () {
        $role = Auth::user()->role;
        return view("dashboard.$role");
    })->name('dashboard');

    // Profil et messages (accessibles à tous les utilisateurs authentifiés)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('messages', MessageController::class);
    Route::get('/messages/inbox', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/sent', [MessageController::class, 'sent'])->name('messages.sent');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('messages/{id}', [MessageController::class, 'show'])->name('messages.show');

    // Routes pour DemandeFondsController (les middlewares sont gérés dans le constructeur)
    Route::resource('demandes-fonds', DemandeFondsController::class);
    Route::get('/demandes-fonds/envois', [DemandeFondsController::class, 'EnvoisFonds'])->name('demandes-fonds.envois');
    Route::post('/demandes-envois/{id}/updateStatus', [DemandeFondsController::class, 'updateStatus'])->name('demandes-envois.updateStatus');
    Route::get('/demandes-fonds/situation', [DemandeFondsController::class, 'SituationFonds'])->name('demandes-fonds.situation');
    Route::get('/demandes-fonds/situationDF', [DemandeFondsController::class, 'SituationDF'])->name('demandes-fonds.situationDF');
    Route::get('/demandes-fonds/situationFE', [DemandeFondsController::class, 'SituationFE'])->name('demandes-fonds.situationFE');
    Route::get('/demandes-fonds/recap', [DemandeFondsController::class, 'Recap'])->name('demandes-fonds.recap');
    Route::get('/demande-fonds/{id}/generate-pdf', [DemandeFondsController::class, 'generatePdf'])->name('demande-fonds.generate.pdf');

    // Routes accessibles uniquement à l'admin
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
        Route::post('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
        Route::resource('postes', PosteController::class);
    });
});

// Ne pas oublier d'inclure les routes d'authentification
require __DIR__.'/auth.php';
