<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DemandeFondsController;
use App\Http\Controllers\RapportPaiementController;
use App\Http\Controllers\ReceptionFondsController;
use App\Http\Controllers\UserController; 
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TresorierController;
use App\Http\Controllers\AcctController;
use App\Http\Controllers\SuperviseurController;
use App\Models\Attachment;
use App\Models\RapportPaiement;
use App\Models\ReceptionFonds;
use App\Models\User;

Route::get('/', function () {
    return view('accueil');
});
Route::get('/accueil', function () {
    return view('accueil');
})->middleware(['auth', 'verified'])->name('accueil');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.inbox');
    Route::get('/messages/sent', [MessageController::class, 'sent'])->name('messages.sent');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::resource('messages', MessageController::class);
    Route::resource('demandes-fonds', DemandeFondsController::class);
    Route::resource('users', UserController::class);
    Route::resource('receptions-fonds', ReceptionFondsController::class);
    Route::resource('rapports-paiements', RapportPaiementController::class);
    Route::resource('attachments', AttachmentController::class);
});

// Routes pour les trésoriers
Route::middleware(['auth', 'role:tresorier'])->group(function () {
    Route::get('/tresorier', [TresorierController::class, 'index'])->name('tresorier.accueil');
    // Ajoutez d'autres routes spécifiques aux trésoriers ici
});

// Routes pour les comptes ACCT
Route::middleware(['auth', 'role:acct'])->group(function () {
    Route::get('/acct', [AcctController::class, 'index'])->name('acct.accueil');
    // Ajoutez d'autres routes spécifiques aux comptes ACCT ici
});

Route::middleware(['auth', 'role:superviseur'])->group(function () {
    Route::get('/superviseur', [SuperviseurController::class, 'index'])->name('superviseur.accueil');
    // Ajoutez d'autres routes spécifiques aux comptes ACCT ici
});

require __DIR__.'/auth.php';
