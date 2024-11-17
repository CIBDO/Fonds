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
        })->orderBy('created_at', 'desc')->paginate(8);

        return view('messages.inbox', compact('messages'));
    }

    // Afficher la boîte d'envoi
    public function sent()
    {
        $messages = Message::where('sender_id', Auth::id())->orderBy('created_at', 'desc')->paginate(8);
        return view('messages.sent', compact('messages'));
    }

    // Afficher le formulaire de nouveau message
    public function create()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('messages.create', compact('users'));
    }

    // Enregistrer un nouveau message
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'receiver_ids' => 'required|array',
            'receiver_ids.*' => 'exists:users,id',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,xlsx,xls,docx,zip|max:51200'
        ]);

        $message = Message::create([
            'subject' => $request->subject,
            'body' => $request->body,
            'sender_id' => Auth::id(),
            'status' => 'unread',
            'sent_at' => now(), // Enregistre l'heure d'envoi
        ]);

        /* foreach ($request->receiver_ids as $receiverId) {
            $recipient = User::find($receiverId);
            if ($recipient) {
                $recipient->notify(new MessageSent($message));
            }
        } */
        foreach ($request->receiver_ids as $receiverId) {
            $recipient = User::find($receiverId);
            if ($recipient) {
                // Attachez le destinataire avec le type 'reception' pour la boîte de réception
                $message->recipients()->attach($receiverId, ['type' => 'reception']);
                $recipient->notify(new MessageSent($message));
            }
        }

         if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->storeAs('attachments', $file->getClientOriginalName(), 'public');
                Attachment::create([
                    'filename' => $file->getClientOriginalName(),
                    'filepath' => $path,
                    'message_id' => $message->id,
                ]);
            }
        } 
        Alert::success('Message envoyé avec succès.');
        return redirect()->route('messages.sent')->with('success', 'Message envoyé avec succès.');
    }

    // Afficher un message spécifique
    public function show($id)
    {
        $message = Message::with(['sender', 'recipients', 'attachments'])->findOrFail($id);

        $recipient = $message->recipients->where('id', Auth::id())->first();

        if ($recipient && is_null($recipient->pivot->received_at)) {
            $message->recipients()->updateExistingPivot(Auth::id(), ['received_at' => now()]);
            // Mettre à jour le statut du message
            $message->update(['status' => 'read']);
        }

        foreach ($message->attachments as $attachment) {
            $attachment->public_url = Storage::url($attachment->filepath);
        }

        return view('messages.show', compact('message', 'recipient'));
    }

    // Afficher les notifications
    public function notifications()
    {
        $notifications = Auth::user()->notifications;
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

    // Répondre à un message
    public function reply($id, Request $request)
    {
        $request->validate([
            'body' => 'required|string',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,xlsx,xls,docx,zip|max:2048'
        ]);

        $originalMessage = Message::findOrFail($id);
        $replyMessage = Message::create([
            'subject' => 'RE: ' . $originalMessage->subject,
            'body' => $request->body,
            'sender_id' => Auth::id(),
            'status' => 'unread',
            'sent_at' => now(), // Enregistre l'heure d'envoi pour la réponse
            'parent_id' => $originalMessage->id,
        ]);

        $replyMessage->recipients()->attach($originalMessage->sender_id, ['type' => 'reception']);
        $originalMessage->sender?->notify(new MessageSent($replyMessage));

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = time() . '-' . $file->getClientOriginalName();
                $filePath = $file->storeAs('public/attachments', $fileName);
                Attachment::create([
                    'message_id' => $replyMessage->id,
                    'filename' => $fileName,
                    'filepath' => 'attachments/' . $fileName,
                ]);
            }
        }

        Alert::success('Réponse envoyée avec succès.');
        return redirect()->route('messages.index')->with('success', 'Réponse envoyée avec succès.');
    }

    // Afficher le formulaire de réponse
    public function showReplyForm($id)
    {
        $message = Message::findOrFail($id);
        return view('messages.reply', compact('message'));
    }

    // Prévisualisation d'un fichier joint
    public function preview($filename)
    {
        $path = public_path('storage/attachments/' . $filename);
        if (!file_exists($path)) {
            abort(404, "Le fichier n'existe pas.");
        }

        $fileType = mime_content_type($path);
        if (str_contains($fileType, 'pdf') || str_contains($fileType, 'image')) {
            return response()->file($path);
        }

        return response()->download($path);
    }

    // Téléchargement d'un fichier joint
    public function downloadAttachment($id)
    {
        $attachment = Attachment::findOrFail($id);
        $filePath = storage_path('app/public/' . $attachment->filepath);

        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return redirect()->back()->with('error', 'Fichier non trouvé.');
    }

    // Afficher le formulaire de transfert de message
    public function forward($id)
    {
        $originalMessage = Message::with(['attachments'])->findOrFail($id);
        $users = User::where('id', '!=', Auth::id())->get();

        return view('messages.forward', compact('originalMessage', 'users'));
    }

    // Traiter le transfert de message
    public function forwardStore(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'receiver_ids' => 'required|array',
            'receiver_ids.*' => 'exists:users,id',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,xlsx,xls,docx,zip|max:51200'
        ]);

        $originalMessage = Message::findOrFail($id);

        $forwardedMessage = Message::create([
            'subject' => 'FW: ' . $originalMessage->subject,
            'body' => $request->body,
            'sender_id' => Auth::id(),
            'status' => 'unread',
            'sent_at' => now(),
        ]);

        foreach ($request->receiver_ids as $receiverId) {
            $forwardedMessage->recipients()->attach($receiverId, ['type' => 'reception']);
            User::find($receiverId)?->notify(new MessageSent($forwardedMessage));
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->storeAs('attachments', $file->getClientOriginalName(), 'public');
                Attachment::create([
                    'filename' => $file->getClientOriginalName(),
                    'filepath' => $path,
                    'message_id' => $forwardedMessage->id,
                ]);
            }
        }

        Alert::success('Message transféré avec succès.');
        return redirect()->route('messages.sent')->with('success', 'Message transféré avec succès.');
    }

    public function replyAll($id, Request $request)
    {
       /*  dd('Méthode replyAll atteinte'); */

        $request->validate([
            'body' => 'required|string',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,xlsx,xls,docx,zip|max:2048'
        ]);

        $originalMessage = Message::with('recipients')->findOrFail($id);

        // Créer le message de réponse
        $replyMessage = Message::create([
            'subject' => 'RE: ' . $originalMessage->subject,
            'body' => $request->body,
            'sender_id' => Auth::id(),
            'status' => 'unread',
            'sent_at' => now(),
            'parent_id' => $originalMessage->id,
        ]);

            // Récupérer les destinataires et l'expéditeur du message original
        $recipientIds = $originalMessage->recipients->pluck('id')->toArray();
        $recipientIds[] = $originalMessage->sender_id; // Inclure l'expéditeur original

        // Exclure l'ID de l'utilisateur actuel pour éviter de s'envoyer le message à soi-même
        $currentUserId = Auth::id();
        $recipientIds = array_filter($recipientIds, fn($id) => $id !== $currentUserId);

        // Attacher les destinataires uniques au message de réponse
        $replyMessage->recipients()->attach(array_unique($recipientIds), ['type' => 'reception']);


        // Envoyer une notification à chaque destinataire
        foreach ($recipientIds as $recipientId) {
            $recipient = User::find($recipientId);
            if ($recipient) {
                $recipient->notify(new MessageSent($replyMessage));
            }
        }

        // Ajouter les pièces jointes
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = time() . '-' . $file->getClientOriginalName();
                $filePath = $file->storeAs('public/attachments', $fileName);
                Attachment::create([
                    'message_id' => $replyMessage->id,
                    'filename' => $fileName,
                    'filepath' => 'attachments/' . $fileName,
                ]);
            }
        }

        Alert::success('Réponse envoyée avec succès à tous les destinataires.');
        return redirect()->route('messages.index')->with('success', 'Réponse envoyée avec succès à tous les destinataires.');
    }

        public function showReplyAllForm($id)
    {
        $message = Message::with('recipients', 'sender')->findOrFail($id);
        return view('messages.replyAll', compact('message'));
    }


}
