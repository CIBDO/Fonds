<?php

namespace App\Notifications;

use App\Models\DemandeFonds;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class DemandeFondsStatusNotification extends Notification
{
    use Queueable;

    protected $demandeFonds;

    public function __construct(DemandeFonds $demandeFonds)
    {
        $this->demandeFonds = $demandeFonds;
    }

    public function via($notifiable)
    {
        // On envoie la notification par base de données et par e-mail
        return ['mail', 'database'];
    }

    // Notification par e-mail
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('Le statut de votre demande a été mis à jour.')
                    ->line('Nouveau statut: ' . $this->demandeFonds->status)
                    ->line('Commentaires: ' . $this->demandeFonds->commentaires)
                    ->action('Voir la demande', url('/demandes-fonds/'.$this->demandeFonds->id))
                    ->line('Merci pour votre patience.');
    }

    // Notification stockée dans la base de données
    public function toDatabase($notifiable)
    {
        return [
            'demande_id' => $this->demandeFonds->id,
            'statut' => $this->demandeFonds->status,
            'commentaires' => $this->demandeFonds->commentaires,
            'updated_at' => $this->demandeFonds->updated_at->toDateTimeString(),
        ];
    }
}

