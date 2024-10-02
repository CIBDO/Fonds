<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageSent extends Notification
{
    use Queueable;

    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast']; // Vous pouvez ajouter d'autres canaux comme 'mail' si nécessaire
    }

    public function toDatabase($notifiable)
    {
        return [
            'message_id' => $this->message->id,
            'sujet' => $this->message->sujet,
            'contenu' => $this->message->contenu,
            'sender_id' => $this->message->sender_id,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return [
            'message_id' => $this->message->id,
            'sujet' => $this->message->sujet,
            'contenu' => $this->message->contenu,
            'sender_id' => $this->message->sender_id,
        ];
    }
}
