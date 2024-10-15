<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\DemandeFonds;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemandeFondsStatusNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public DemandeFonds $demande)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusMessage = $this->demande->status == 'approuve' ? 'approuvée' : 'rejetée';
        
        return (new MailMessage)
                    ->subject('Statut de votre demande de fonds mis à jour')
                    ->line("Votre demande de fonds pour le mois de {$this->demande->mois} a été {$statusMessage}.")
                    ->line("Montant : " . number_format($this->demande->montant, 0, ',', ' ') . " FCFA")
                    ->line("Observation : {$this->demande->observation}")
                    ->action('Voir la demande', route('demandes-fonds.show', $this->demande->id))
                    ->line('Merci d\'utiliser notre application!');
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'demande_id' => $this->demande->id,
            'status' => $this->demande->status,
            'message' => "Votre demande de fonds pour le mois de {$this->demande->mois} a été mise à jour.",
        ];
    }
}
