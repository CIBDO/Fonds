<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

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

   /*  public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Vous avez reçu un nouveau message')
            ->greeting('Bonjour ' . $notifiable->name)
            ->line("Vous avez un nouveau message de {$this->message->sender->name}.")
            ->line("Sujet du message : {$this->message->subject}")
            ->line('Cliquez sur le lien ci-dessous pour consulter le message.')
            ->action('Voir le message', route('messages.index', $this->message->id)) // Lien vers le message
            ->from('bdokeita100@gmail.com', 'Système de gestion des fonds');
    }
 */

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
