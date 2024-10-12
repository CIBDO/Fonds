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
use App\Http\Controllers\PosteController;
use App\Http\Controllers\EnvoisFondsController;
use App\Http\Controllers\SuperviseurController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Attachment;
use App\Models\RapportPaiement;
use App\Models\ReceptionFonds;
use App\Models\User;

Route::get('/', function () {
    return view('auth.login');
})->name('custom.login');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/messages/inbox', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/sent', [MessageController::class, 'sent'])->name('messages.sent');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('messages/{id}', [MessageController::class, 'show'])->name('messages.show'); 
    
    Route::get('/notifications', [MessageController::class, 'notifications'])->name('messages.notifications');
    Route::resource('demandes-fonds', DemandeFondsController::class);
    Route::resource('envois-fonds', EnvoisFondsController::class);
    Route::resource('users', UserController::class);
    Route::resource('receptions-fonds', ReceptionFondsController::class);
    Route::resource('rapports-paiements', RapportPaiementController::class);
    Route::resource('attachments', AttachmentController::class);
    Route::resource('postes', PosteController::class);
    Route::post('/notifications/{notification}/mark-as-read', [MessageController::class, 'markAsRead'])->name('markAsRead');
    Route::delete('/notifications/{notification}', [MessageController::class, 'deleteNotification'])->name('deleteNotification');  
    /* Route::post('/messages/{id}/reply', [App\Http\Controllers\MessageController::class, 'reply'])->name('messages.reply'); */
    Route::post('messages/{id}/reply', [MessageController::class, 'reply'])->name('messages.reply');
    Route::get('messages/{id}/reply', [MessageController::class, 'showReplyForm'])->name('messages.reply.form');
    Route::get('attachments/preview/{filename}', [MessageController::class, 'preview'])->name('attachments.preview');
    Route::get('attachments/download/{id}', [MessageController::class, 'downloadAttachment'])->name('attachments.download');
    Route::get('/demande-fonds/{id}/generate-pdf', [DemandeFondsController::class, 'generatePdf'])->name('demande-fonds.generate.pdf');
    Route::patch('/demandes-fonds/{id}/update-status', [DemandeFondsController::class, 'updateStatus'])->name('demandes-fonds.update-status');



});

// Routes pour les trésoriers
Route::middleware(['auth', 'role:tresorier'])->group(function () {
    Route::get('/tresorier', [TresorierController::class, 'index'])->name('tresorier.dashboard');
    // Ajoutez d'autres routes spécifiques aux trésoriers ici
});

// Routes pour les comptes ACCT
Route::middleware(['auth', 'role:acct'])->group(function () {
    Route::get('/acct', [AcctController::class, 'index'])->name('acct.dashboard');
    // Ajoutez d'autres routes spécifiques aux comptes ACCT ici
});

Route::middleware(['auth', 'role:superviseur'])->group(function () {
    Route::get('/superviseur', [SuperviseurController::class, 'index'])->name('superviseur.dashboard');
    // Ajoutez d'autres routes spécifiques aux comptes ACCT ici
});

require __DIR__.'/auth.php';