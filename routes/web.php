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

// Contrôleurs PCS
use App\Http\Controllers\PCS\{
    BureauDouaneController,
    DeclarationPcsController,
    AutreDemandeController,
};

// Contrôleurs TRIE
use App\Http\Controllers\TRIE\{
    BureauTrieController,
    CotisationTrieController,
    EtatTrieController,
};

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Route pour la page d'accueil (connexion)
Route::get('/', fn() => view('auth.login'))->name('custom.login');
Route::get('/demandes-fonds/totaux-par-mois', [DemandeFondsController::class, 'totauxParMois'])->name('demandes-fonds.totaux-par-mois');
// Route pour le tableau de bord
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user && $user->role === 'admin') {
        return redirect()->route('dashboard.admin');
    } elseif ($user && $user->role === 'tresorier') {
        return redirect()->route('dashboard.tresorier');
    } elseif ($user && $user->role === 'acct') {
        return redirect()->route('dashboard.acct');
    }
    // Ajoutez une redirection par défaut ou une gestion d'erreur ici si nécessaire
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    // Route pour les demandes de fonds
    /* Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard'); */
    Route::get('/demandes-fonds/recettes', [DemandeFondsController::class, 'Recettes'])
        ->middleware('role:acct,admin,superviseur,tresorier') // Tous les rôles sauf trésorier
        ->name('demandes-fonds.recettes');
    Route::get('/demandes-fonds/solde', [DemandeFondsController::class, 'Solde'])
        ->middleware('role:acct,admin,superviseur,tresorier') // Tous les rôles sauf trésorier
        ->name('demandes-fonds.solde');
    Route::get('/demandes-fonds/fonctionnaires', [DemandeFondsController::class, 'Fonctionnaires'])
        ->middleware('role:acct,admin,superviseur,tresorier') // Tous les rôles sauf trésorier
        ->name('demandes-fonds.fonctionnaires');
    Route::get('/demandes-fonds/situation-mensuelle', [DemandeFondsController::class, 'situationMensuelle'])
        ->middleware('role:acct,admin,superviseur') // Accès pour admin, acct et superviseur
        ->name('demandes-fonds.situation-mensuelle');
    Route::get('/demandes-fonds/etat-avant-envoi', [DemandeFondsController::class, 'etatAvantEnvoi'])
        ->middleware('role:acct,admin,superviseur') // Accès pour admin, acct et superviseur
        ->name('demandes-fonds.etat-avant-envoi');
    Route::get('/demandes-fonds/etat-detaille-avant-envoi', [DemandeFondsController::class, 'etatDetailleAvantEnvoi'])
        ->middleware('role:acct,admin,superviseur') // Accès pour admin, acct et superviseur
        ->name('demandes-fonds.etat-detaille-avant-envoi');
    Route::get('/notifications', [MessageController::class, 'notifications'])->name('messages.notifications');
    Route::get('/demandes/export', [DemandeFondsController::class, 'export'])->name('demandes-fonds.export');
    Route::get('/demandes-fonds/detail', [DemandeFondsController::class, 'Detail'])->name('demandes-fonds.detail');
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

    // Routes pour la vue consolidée
    Route::get('/demandes-fonds/consolide', [DemandeFondsController::class, 'consolide'])
        ->middleware('role:acct,admin,superviseur')
        ->name('demandes-fonds.consolide');
    Route::get('/demandes-fonds/consolide/export-csv', [DemandeFondsController::class, 'consolideExportCsv'])
        ->middleware('role:acct,admin,superviseur')
        ->name('demandes-fonds.consolide.export-csv');
Route::get('/demandes-fonds/consolide/export-pdf', [DemandeFondsController::class, 'consolideExportPdf'])
    ->middleware('role:acct,admin,superviseur')
    ->name('demandes-fonds.consolide.export-pdf');

// Vue consolidée détaillée par type de personnel
Route::get('/demandes-fonds/consolide-detaille', [DemandeFondsController::class, 'consolideDetaille'])
    ->middleware('role:acct,admin,superviseur')
    ->name('demandes-fonds.consolide-detaille');
Route::get('/demandes-fonds/consolide-detaille/export-csv', [DemandeFondsController::class, 'consolideDetailleExportCsv'])
    ->middleware('role:acct,admin,superviseur')
    ->name('demandes-fonds.consolide-detaille.export-csv');
Route::get('/demandes-fonds/consolide-detaille/export-pdf', [DemandeFondsController::class, 'consolideDetailleExportPdf'])
    ->middleware('role:acct,admin,superviseur')
    ->name('demandes-fonds.consolide-detaille.export-pdf');

    Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
    Route::post('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');



    // Routes spécifiques pour la création et la mise à jour des demandes de fonds
    Route::middleware(['role:tresorier,admin'])->group(function () {
        Route::resource('demandes-fonds', DemandeFondsController::class); // Ajouter la route index
    });


    // Accès public pour générer PDF
    Route::get('/demande-fonds/{id}/generate-pdf', [DemandeFondsController::class, 'generatePdf'])->name('demande-fonds.generate.pdf');

    // Route pour générer le PDF consolidé des demandes par mois
    Route::get('/demandes-fonds/mois/{mois}/{annee}/pdf', [DemandeFondsController::class, 'generateMonthlyPdf'])
        ->name('demandes-fonds.monthly.pdf')
        ->middleware('role:acct,admin,superviseur,tresorier');

    // Autres routes protégées
    Route::resource('users', UserController::class);
    Route::resource('receptions-fonds', ReceptionFondsController::class);
    Route::resource('rapports-paiements', RapportPaiementController::class);
    Route::resource('attachments', AttachmentController::class);
    Route::resource('postes', PosteController::class);

    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    Route::delete('/notifications/{notification}', [MessageController::class, 'deleteNotification'])->name('deleteNotification');
    Route::post('messages/{id}/reply', [MessageController::class, 'reply'])->name('messages.reply');
    Route::get('messages/{id}/reply', [MessageController::class, 'showReplyForm'])->name('messages.reply.form');
    Route::get('attachments/preview/{filename}', [MessageController::class, 'preview'])->name('attachments.preview');
    Route::get('attachments/download/{id}', [MessageController::class, 'downloadAttachment'])->name('attachments.download');
    Route::get('/messages/{id}/forward', [MessageController::class, 'forward'])->name('messages.forward');
    Route::post('/messages/{id}/forward', [MessageController::class, 'forwardStore'])->name('messages.forward.store');

    Route::get('/messages/{id}/replyAll', [MessageController::class, 'replyAll'])->name('messages.replyAll');
    Route::post('/messages/{id}/replyAll', [MessageController::class, 'replyAllStore'])->name('messages.replyAll.store');

    Route::get('/messages/{id}/replyAll', [MessageController::class, 'showReplyAllForm'])->name('messages.replyAllForm');
    Route::post('/messages/{id}/replyAll', [MessageController::class, 'replyAll'])->name('messages.replyAll');


});

// Routes pour les trésoriers
Route::middleware(['auth', 'role:tresorier'])->group(function () {
    Route::get('/tresorier', [TresorierController::class, 'index'])->name('dashboard.tresorier');
    Route::get('/demandes-fonds', [DemandeFondsController::class, 'index'])->name('demandes-fonds.index');
    Route::resource('demandes-fonds', DemandeFondsController::class);

});

// Routes pour les comptes ACCT
Route::middleware(['auth', 'role:acct,superviseur,tresorier,admin',])->group(function () {
    Route::get('/acct', [AcctController::class, 'index'])->name('dashboard.acct');
    Route::put('/demandes-fonds/{id}/update-status', [DemandeFondsController::class, 'updateStatus'])->name('demandes-fonds.update-status');
    Route::get('/demandes-fonds', [DemandeFondsController::class, 'index'])->name('demandes-fonds.index');
    Route::resource('demandes-fonds', DemandeFondsController::class);
    Route::get('/demandes-fonds/envois', [DemandeFondsController::class, 'EnvoisFonds'])->name('demandes-fonds.envois');
    Route::get('/demandes-fonds/situation', [DemandeFondsController::class, 'SituationFonds'])->name('demandes-fonds.situation');
    Route::get('/demandes-fonds/situationDF', [DemandeFondsController::class, 'SituationDF'])->name('demandes-fonds.situationDF');
    Route::get('/demandes-fonds/situationFE', [DemandeFondsController::class, 'SituationFE'])->name('demandes-fonds.situationFE');
    Route::get('/demandes-fonds/recap', [DemandeFondsController::class, 'Recap'])->name('demandes-fonds.recap');
    Route::get('/demandes-fonds/paiement', [DemandeFondsController::class, 'Paiement'])->name('demandes-fonds.paiement');
    Route::get('/demandes-fonds/situation-mensuelle', [DemandeFondsController::class, 'situationMensuelle'])->name('demandes-fonds.situation-mensuelle');
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
    Route::get('/admin', [AdminController::class, 'index'])->name('dashboard.admin');
});

// ===============================================================
// ROUTES PCS (Programme de Consolidation des Statistiques)
// ===============================================================

Route::middleware(['auth'])->prefix('pcs')->name('pcs.')->group(function () {

    // ===== BUREAUX DE DOUANES (Gestion) =====
    Route::middleware('role:admin')->group(function () {
        Route::resource('bureaux', BureauDouaneController::class)->except(['show']);
        Route::post('bureaux/{bureau}/toggle-actif', [BureauDouaneController::class, 'toggleActif'])
            ->name('bureaux.toggle-actif');
    });

    // ===== DÉCLARATIONS PCS =====
    Route::controller(DeclarationPcsController::class)->prefix('declarations')->name('declarations.')->group(function () {
        // Liste et formulaire (tous les utilisateurs PCS)
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('{declaration}', 'show')->name('show');
        Route::get('{declaration}/edit', 'edit')->name('edit');
        Route::put('{declaration}', 'update')->name('update');

        // Validation (ACCT uniquement)
        // DÉSACTIVÉ : La soumission passe directement au statut "valide", plus besoin de validation
        /*
        Route::middleware('role:admin,acct')->group(function () {
            Route::post('{declaration}/valider', 'valider')->name('valider');
            Route::post('{declaration}/rejeter', 'rejeter')->name('rejeter');
        });
        */

        // Génération états PDF/Excel
        Route::get('pdf/recettes', 'generatePdfRecettes')->name('pdf.recettes');
        Route::get('pdf/reversements', 'generatePdfReversements')->name('pdf.reversements');

        // États consolidés (ACCT uniquement)
        Route::middleware('role:admin,acct')->group(function () {
            Route::get('etat-consolide/reversements', 'etatConsolideReversements')->name('etat-consolide.reversements');

            // Filtrage et génération d'états personnalisés pour déclarations
            Route::get('filtre-etat', 'filtreEtat')->name('declarations.filtre-etat');
            Route::get('etat-consolide/filtre', 'etatConsolideFiltre')->name('declarations.etat-consolide.filtre');
            Route::get('stats-rapides', 'statsRapides')->name('declarations.stats-rapides');
            Route::get('apercu', 'apercu')->name('declarations.apercu');
        });

        // États consolidés pour postes émetteurs
        Route::get('etat-consolide/poste-emetteur', 'etatConsolidePosteEmetteur')->name('etat-consolide.poste-emetteur');
    });

    // ===== ÉTATS CONSOLIDÉS UNIFIÉS =====
    Route::middleware('role:admin,acct')->controller(\App\Http\Controllers\PCS\EtatsConsolidesController::class)->prefix('etats-consolides')->name('etats-consolides.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('generer', 'generer')->name('generer');
        Route::get('apercu', 'apercu')->name('apercu');
        Route::get('stats', 'stats')->name('stats');
        Route::get('donnees-uemoa-aes', 'getDonneesUemoaAes')->name('donnees-uemoa-aes');
    });

    // ===== DÉSTOCKAGES PCS (ACCT uniquement) =====
    Route::middleware('role:admin,acct')->controller(\App\Http\Controllers\PCS\DestockagePcsController::class)->prefix('destockages')->name('destockages.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('collecte', 'collecte')->name('collecte');
        Route::get('etats', 'etats')->name('etats');
        Route::get('create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('pdf/etat-collecte', 'etatCollectePdf')->name('pdf.etat-collecte');
        Route::get('pdf/etat-consolide', 'etatConsolidePdf')->name('pdf.etat-consolide');
        Route::get('{destockage}', 'show')->name('show');
        Route::get('{destockage}/pdf', 'pdf')->name('pdf');
    });

    // ===== AUTRES DEMANDES =====
    Route::controller(AutreDemandeController::class)->prefix('autres-demandes')->name('autres-demandes.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('{demande}', 'show')->name('show');
        Route::get('{demande}/edit', 'edit')->name('edit');
        Route::put('{demande}', 'update')->name('update');
        Route::delete('{demande}', 'destroy')->name('destroy');

        // Validation
        Route::middleware('role:admin,acct')->group(function () {
            Route::post('{demande}/valider', 'valider')->name('valider');
            Route::post('{demande}/rejeter', 'rejeter')->name('rejeter');

            // États consolidés
            Route::get('etat-consolide/autres-demandes', 'etatConsolideAutresDemandes')->name('etat-consolide.autres-demandes');

            // Filtrage et génération d'états personnalisés
            Route::get('filtre-etat', 'filtreEtat')->name('filtre-etat');
            Route::get('etat-consolide/filtre', 'etatConsolideFiltre')->name('etat-consolide.filtre');
            Route::get('stats-rapides', 'statsRapides')->name('stats-rapides');
            Route::get('apercu', 'apercu')->name('apercu');
        });

        // États consolidés pour postes émetteurs
        Route::get('etat-consolide/poste-emetteur', 'etatConsolidePosteEmetteur')->name('etat-consolide.poste-emetteur');

        // Statistiques
        Route::get('statistiques/index', 'statistiques')->name('statistiques');
    });
}); // FIN DU GROUPE PCS

// ===== MODULE TRIE - COTISATIONS CCIM =====
Route::middleware(['auth'])->prefix('trie')->name('trie.')->group(function () {

        // ===== BUREAUX TRIE =====
        Route::controller(BureauTrieController::class)->prefix('bureaux')->name('bureaux.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('{poste}/manage', 'manage')->name('manage');
            Route::post('/', 'store')->name('store');
            Route::put('{bureau}', 'update')->name('update');
            Route::patch('{bureau}/toggle-status', 'toggleStatus')->name('toggle-status');
            Route::delete('{bureau}', 'destroy')->name('destroy');

            // API pour récupérer les bureaux d'un poste
            Route::get('api/{poste}/bureaux', 'getBureaux')->name('api.bureaux');
        });

    // ===== COTISATIONS TRIE =====
    Route::controller(CotisationTrieController::class)->prefix('cotisations')->name('cotisations.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        
        // API pour récupérer les mois renseignés (doit être avant les routes avec paramètres)
        Route::get('mois-renseignes', 'getMoisRenseignes')->name('mois-renseignes');
        
        Route::get('{cotisation}', 'show')->name('show');
        Route::get('{cotisation}/edit', 'edit')->name('edit');
        Route::put('{cotisation}', 'update')->name('update');
        Route::delete('{cotisation}', 'destroy')->name('destroy');

        // États consolidés pour postes émetteurs
        Route::get('etat-consolide/poste-emetteur', 'etatConsolidePosteEmetteur')->name('etat-consolide.poste-emetteur');
    });

    // ===== ÉTATS TRIE =====
    Route::controller(EtatTrieController::class)->prefix('etats')->name('etats.')->group(function () {
        Route::get('/', 'index')->name('index');

        // Génération des PDF (ACCT/Admin uniquement)
        Route::middleware('role:admin,acct')->group(function () {
            Route::get('mensuel', 'etatMensuel')->name('mensuel');
            Route::get('consolide', 'etatConsolide')->name('consolide');
        });
    });
}); // FIN DU GROUPE TRIE

// Ajoutez la route d'authentification
require __DIR__.'/auth.php';
