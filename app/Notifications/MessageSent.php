<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
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
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message_id' => $this->message->id,
            'subject' => $this->message->subject,
            'sender_name' => "Vous avez un nouveau message de {$this->message->sender->name}",
            'url' => route('messages.index', $this->message->id)
            // Ajoutez d'autres données si nécessaire
        ];
    }
}
