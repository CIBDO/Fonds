<?php

namespace App\Notifications;

use App\Models\DemandeFonds;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemandeFondsStatusNotification extends Notification
{
    use Queueable;

    public function __construct(public DemandeFonds $demande)
    {
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $statusMessage = $this->demande->status == 'approuve' ? 'approuvée' : 'rejetée';
        $montant = number_format((float) ($this->demande->montant ?? 0), 0, ',', ' ');

        return (new MailMessage)
            ->subject('Mise à jour du statut de votre demande de fonds')
            ->greeting('Bonjour '.($notifiable->name ?? ''))
            ->line("Votre demande de fonds pour le mois de {$this->demande->mois} a été {$statusMessage}.")
            ->line("Montant : {$montant} FCFA")
            ->line('Observation : '.($this->demande->observation ?: '—'))
            ->action('Voir la situation', route('demandes-fonds.situation'))
            ->line('Merci de votre diligence.');
    }

    public function toArray($notifiable)
    {
        $statusMessage = $this->demande->status == 'approuve' ? 'approuvée' : 'rejetée';

        return [
            'demande_id' => $this->demande->id,
            'status' => $this->demande->status,
            'message' => "Votre demande de fonds pour le mois de {$this->demande->mois} a été {$statusMessage}",
            'montant' => $this->demande->montant,
            'observation' => $this->demande->observation,
            'type' => 'status_update',
            'url' => route('demandes-fonds.situation'),
        ];
    }
}
