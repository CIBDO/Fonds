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
            'sujet' => $this->message->subject,
            'contenu' => $this->message->body,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'type' => 'message'
        ];
    }
}
