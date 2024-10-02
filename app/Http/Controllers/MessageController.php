<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\MessageSent;

class MessageController extends Controller
{
    // Afficher la boîte de réception
    public function index()
    {
        // Récupère les messages reçus par l'utilisateur connecté
        $messages = Message::where('receiver_id', Auth::id())->get();
        return view('messages.inbox', compact('messages'));
    }

    // Afficher la boîte d'envoi
    public function sent()
    {
        // Récupère les messages envoyés par l'utilisateur connecté
        $messages = Message::where('sender_id', Auth::id())->get();
        return view('messages.sent', compact('messages'));
    }

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
        ]);
    
        // Attacher les destinataires au message
        foreach ($request->receiver_ids as $receiverId) {
            $message->recipients()->attach($receiverId, ['type' => 'reception']);
            // Envoyer une notification à chaque destinataire
            $user = User::find($receiverId);
            $user->notify(new MessageSent($message));
        }
    
        // Gestion des fichiers joints
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments'); // Stocker le fichier
    
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
    public function show(Message $message)
    {
        // Vérifie si l'utilisateur courant est le destinataire ou l'expéditeur
        if ($message->receiver_id !== Auth::id() && $message->sender_id !== Auth::id()) {
            abort(403, 'Accès interdit.');
        }
    
        // Marque le message comme lu pour le destinataire courant
        $message->recipients()->updateExistingPivot(Auth::id(), ['lu' => true]);
    
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
}
