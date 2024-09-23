<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    // Afficher la boîte de réception
    public function index()
    {
        // Récupère les messages reçus par l'utilisateur connecté
        $messages = Auth::user()->messages()->wherePivot('type', 'reception')->get();
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
        $request->validate([
            'sujet' => 'required|string|max:255',
            'contenu' => 'required|string',
            'destinataires' => 'required|array',
            'fichiers.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:2048'
        ]);

        // Création du message
        $message = Message::create([
            'sujet' => $request->sujet,
            'contenu' => $request->contenu,
            'sender_id' => Auth::id(),
        ]);

        // Attachement des destinataires au message
        foreach ($request->destinataires as $destinataireId) {
            $message->recipients()->attach($destinataireId, ['type' => 'reception']);
        }

        // Attachement de l'utilisateur courant dans la boîte d'envoi
        $message->recipients()->attach(Auth::id(), ['type' => 'envoi']);

        // Enregistrement des pièces jointes
        if ($request->hasFile('fichiers')) {
            foreach ($request->file('fichiers') as $file) {
                $path = $file->store('attachments');
                Attachment::create([
                    'nom_fichier' => $file->getClientOriginalName(),
                    'chemin' => $path,
                    'message_id' => $message->id,
                ]);
            }
        }

        return redirect()->route('messages.sent')->with('success', 'Message envoyé avec succès.');
    }

    // Afficher un message spécifique
    public function show(Message $message)
    {
        // Vérifie que l'utilisateur est autorisé à voir ce message
        $this->authorize('view', $message);

        // Marque le message comme lu pour le destinataire courant
        $message->recipients()->updateExistingPivot(Auth::id(), ['lu' => true]);

        return view('messages.show', compact('message'));
    }
}
