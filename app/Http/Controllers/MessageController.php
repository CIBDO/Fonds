<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\MessageSent;
use RealRashid\SweetAlert\Facades\Alert;

class MessageController extends Controller
{
    
    // Afficher la boîte de réception
    public function index()
    {
        // Récupère les messages reçus par l'utilisateur connecté via la table pivot et les trie par ordre décroissant
        $messages = Message::whereHas('recipients', function ($query) {
            $query->where('user_id', Auth::id());
        })->orderBy('created_at', 'desc')->get(); // Ajout du tri par date de création
        foreach ($messages as $message) {
            $message->recipient->notify(new MessageSent($message));
        }
        return view('messages.inbox', compact('messages'));
    }

    // Afficher la boîte d'envoi
    public function sent()
    {
        // Récupère les messages envoyés par l'utilisateur connecté
        $messages = Message::where('sender_id', Auth::id())->orderBy('created_at', 'desc') ->get();
        return view('messages.sent', compact('messages'));
    }

    /* // Afficher les messages reçus
    public function received()
    {
        // Récupère les messages reçus par l'utilisateur connecté
        $messages = Message::whereHas('recipients', function ($query) {
            $query->where('user_id', Auth::id());
        })->get();
        return view('messages.received', compact('messages'));
    } */

    // Afficher le formulaire de nouveau message
    public function create()
    {
        // Récupère les utilisateurs (destinataires potentiels), excluant l'utilisateur connecté
        $users = User::where('id', '!=', Auth::id())->get();
        return view('messages.create', compact('users'));
    }

    // Enregistrer un nouveau message
    public function store(Request $request)
{
    // Validation des données du formulaire
    $request->validate([
        'subject' => 'required|string|max:255',
        'body' => 'required|string',
        'receiver_ids' => 'required|array', // Validation des destinataires
        'receiver_ids.*' => 'exists:users,id', // Validation des destinataires
        'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:2048' // Validation des fichiers joints
    ]);
    
    // Création du message
    $message = Message::create([
        'subject' => $request->subject,
        'body' => $request->body,
        'sender_id' => Auth::id(),
        'status' => 'unread', // Le message est initialement non lu
        'sent_at' => now(), // Date et heure d'envoi
    ]);

    // Attacher les destinataires au message
        // Attacher les destinataires et envoyer les notifications
    foreach ($request->receiver_ids as $receiverId) {
        $message->recipients()->attach($receiverId, [
            'type' => 'reception',
            'read_at' => null
        ]);
        
        // Envoyer la notification immédiatement
        $recipient = User::find($receiverId);
        $recipient->notify(new MessageSent($message));
    }

    // Gestion des fichiers joints
   // Gestion des fichiers joints
if ($request->hasFile('attachments')) {
    foreach ($request->file('attachments') as $file) {
        // Stocker les fichiers dans le dossier public via le lien symbolique
        $path = $file->storeAs('attachments', $file->getClientOriginalName(), 'public');
        Attachment::create([
            'filename' => $file->getClientOriginalName(),
            'filepath' => $path,
            'message_id' => $message->id,
        ]);
    }
}


    // Redirection après l'envoi du message
    return redirect()->route('messages.sent')->with('success', 'Message envoyé avec succès.');
}

    
    // Afficher un message spécifique
    public function show($id)
    {
        // Récupérer le message par ID et vérifier si l'utilisateur a accès
        $message = Message::with(['sender', 'recipients', 'attachments'])->findOrFail($id);

        // Vérifier si l'utilisateur connecté est un destinataire du message
        $userIsRecipient = $message->recipients->contains(Auth::id());

        // Marquer comme lu et enregistrer la date de lecture
        if ($userIsRecipient && !$message->read_at) {
            $message->recipients()->updateExistingPivot(Auth::id(), [
                'read_at' => now()
            ]);
            
            // Marquer les notifications comme lues
            Auth::user()
                ->notifications()
                ->where('type', MessageSent::class)
                ->where('data->message_id', $message->id)
                ->update(['read_at' => now()]);
        }
        
            foreach ($message->attachments as $attachment) {
                // Utiliser le lien symbolique pour accéder aux fichiers
                $attachment->public_url = Storage::url($attachment->filepath);
            }

    
        return view('messages.show', compact('message'));
    }
    

    
    // Afficher les notifications
    public function notifications()
    {
        $notifications = Auth::user()->notifications; // Récupérer les notifications de l'utilisateur connecté
        return view('messages.notifications', compact('notifications'));
    }

    // Marquer une notification comme lue
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect()->back()->with('success', 'Notification marquée comme lue.');
    }

    // Supprimer une notification
    public function deleteNotification($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        return redirect()->back()->with('success', 'Notification supprimée avec succès.');
    }

    public function reply($id, Request $request)
{
    // Validation des données de la réponse
    $request->validate([
        'body' => 'required|string',
        'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:2048' // Validation des fichiers joints
    ]);

    // Récupération du message original
    $originalMessage = Message::findOrFail($id);

    // Création de la réponse comme un nouveau message
    $replyMessage = Message::create([
        'subject' => 'RE: ' . $originalMessage->subject,  // Préfixe de réponse
        'body' => $request->body,
        'sender_id' => Auth::id(),
        'status' => 'unread', // Le message est initialement non lu
        'parent_id' => $originalMessage->id,  // Référence au message original
    ]);

    // Attacher l'expéditeur original comme destinataire de la réponse
    $replyMessage->recipients()->attach($originalMessage->sender_id, ['type' => 'reception']);

    // Notifier l'expéditeur original de la réponse
    $originalMessage->sender->notify(new MessageSent($replyMessage));

    // Gestion des fichiers joints (si présents)
    if ($request->hasFile('attachments')) {
        foreach ($request->file('attachments') as $file) {
            $fileName = time() . '-' . $file->getClientOriginalName();
            $filePath = $file->storeAs('public/attachments/attachments', $fileName);

            // Enregistrer les informations du fichier joint
            Attachment::create([
                'message_id' => $replyMessage->id, // Utiliser $replyMessage au lieu de $message
                'filename' => $fileName,
                'filepath' => 'attachments/attachments/' . $fileName,
            ]);
        }
    }
    
    // Redirection vers la boîte de réception après l'envoi de la réponse
    return redirect()->route('messages.index')->with('success', 'Réponse envoyée avec succès.');
}
public function showReplyForm($id)
{
    $message = Message::findOrFail($id); // Récupération du message
    return view('messages.reply', compact('message')); // Retourne la vue de réponse avec le message
}

public function preview($filename)
{
    // Utiliser le chemin public pour la prévisualisation
    $path = public_path('storage/attachments/' . $filename);
    // Vérifier si le fichier existe
    if (!file_exists($path)) {
        abort(404, "Le fichier n'existe pas.");
    }

    // Obtenir le type MIME du fichier
    $fileType = mime_content_type($path);

    // Si le fichier est une image ou un PDF, on le prévisualise
    if (str_contains($fileType, 'pdf') || str_contains($fileType, 'image')) {
        return response()->file($path);
    }

    // Pour les autres types de fichiers, proposer un téléchargement
    return response()->download($path);
}


public function downloadAttachment($id)
{
    // Utiliser le chemin public pour le téléchargement
    $attachment = Attachment::findOrFail($id);
    $filePath = public_path('storage/' . $attachment->filepath);

    if (file_exists($filePath)) {
        return response()->download($filePath);
    }

    return redirect()->back()->with('error', 'Fichier non trouvé.');
}

}
