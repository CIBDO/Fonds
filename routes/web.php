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
    NotificationController,
};

use Illuminate\Support\Facades\Route;

// Route pour la page d'accueil (connexion)
Route::get('/', fn() => view('auth.login'))->name('custom.login');

// Route pour le tableau de bord
Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])->name('dashboard');

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    // Route pour les demandes de fonds
    /* Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard'); */
    Route::get('/notifications', [MessageController::class, 'notifications'])->name('messages.notifications');
    
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/demandes-fonds/envois', [DemandeFondsController::class, 'EnvoisFonds'])
        ->middleware('role:acct,admin,superviseur') // Accès pour tous sauf trésorier
        ->name('demandes-fonds.envois');

    Route::get('/demandes-fonds/situation', [DemandeFondsController::class, 'SituationFonds'])
        ->middleware('role:acct,admin,superviseur,tresorier') // Tous les rôles sauf trésorier
        ->name('demandes-fonds.situation');

    Route::get('/demandes-fonds/situationDF', [DemandeFondsController::class, 'SituationDF'])
        ->middleware('role:acct,admin,superviseur') // Tous les rôles sauf trésorier
        ->name('demandes-fonds.situationDF');

    Route::get('/demandes-fonds/situationFE', [DemandeFondsController::class, 'SituationFE'])
        ->middleware('role:acct,admin,superviseur') // Tous les rôles sauf trésorier
        ->name('demandes-fonds.situationFE');

    Route::get('/demandes-fonds/recap', [DemandeFondsController::class, 'Recap'])
        ->middleware('role:acct,admin,superviseur') // Tous les rôles sauf trésorier
        ->name('demandes-fonds.recap');

    Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
    Route::post('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');

    // Routes spécifiques pour la création et la mise à jour des demandes de fonds
    Route::middleware(['role:tresorier,admin'])->group(function () {
        Route::resource('demandes-fonds', DemandeFondsController::class); // Ajouter la route index
    });
    
    
    // Accès public pour générer PDF
    Route::get('/demande-fonds/{id}/generate-pdf', [DemandeFondsController::class, 'generatePdf'])->name('demande-fonds.generate.pdf');

    // Autres routes protégées
    Route::resource('users', UserController::class);
    Route::resource('receptions-fonds', ReceptionFondsController::class);
    Route::resource('rapports-paiements', RapportPaiementController::class);
    Route::resource('attachments', AttachmentController::class);
    Route::resource('postes', PosteController::class);

    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/{notification}', [MessageController::class, 'deleteNotification'])->name('deleteNotification');
    Route::post('messages/{id}/reply', [MessageController::class, 'reply'])->name('messages.reply');
    Route::get('messages/{id}/reply', [MessageController::class, 'showReplyForm'])->name('messages.reply.form');
    Route::get('attachments/preview/{filename}', [MessageController::class, 'preview'])->name('attachments.preview');
    Route::get('attachments/download/{id}', [MessageController::class, 'downloadAttachment'])->name('attachments.download');
});

// Routes pour les trésoriers
Route::middleware(['auth', 'role:tresorier'])->group(function () {
    Route::get('/tresorier', [TresorierController::class, 'index'])->name('tresorier.dashboard');
    Route::get('/demandes-fonds', [DemandeFondsController::class, 'index'])->name('demandes-fonds.index');
    Route::resource('demandes-fonds', DemandeFondsController::class);

});

// Routes pour les comptes ACCT
Route::middleware(['auth', 'role:acct,superviseur,tresorier,admin',])->group(function () {
    Route::get('/acct', [AcctController::class, 'index'])->name('acct.dashboard');
    Route::put('/demandes-fonds/{id}/update-status', [DemandeFondsController::class, 'updateStatus'])->name('demandes-fonds.update-status');
    Route::get('/demandes-fonds', [DemandeFondsController::class, 'index'])->name('demandes-fonds.index');
    Route::resource('demandes-fonds', DemandeFondsController::class);
    Route::get('/demandes-fonds/envois', [DemandeFondsController::class, 'EnvoisFonds'])->name('demandes-fonds.envois');
    Route::get('/demandes-fonds/situation', [DemandeFondsController::class, 'SituationFonds'])->name('demandes-fonds.situation');
    Route::get('/demandes-fonds/situationDF', [DemandeFondsController::class, 'SituationDF'])->name('demandes-fonds.situationDF');
    Route::get('/demandes-fonds/situationFE', [DemandeFondsController::class, 'SituationFE'])->name('demandes-fonds.situationFE');
    Route::get('/demandes-fonds/recap', [DemandeFondsController::class, 'Recap'])->name('demandes-fonds.recap');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/messages/inbox', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/sent', [MessageController::class, 'sent'])->name('messages.sent');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('messages/{id}', [MessageController::class, 'show'])->name('messages.show'); 

});

// Routes pour les superviseurs
Route::middleware(['auth', 'role:superviseur'])->group(function () {
    Route::get('/superviseur', [SuperviseurController::class, 'index'])->name('superviseur.dashboard');
});

// Routes pour les admins
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
});

// Ajoutez la route d'authentification
require __DIR__.'/auth.php';
