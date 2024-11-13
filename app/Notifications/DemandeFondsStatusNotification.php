<?php

namespace App\Notifications;

use App\Models\DemandeFonds;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
class DemandeFondsStatusNotification extends Notification
{
    use Queueable;

    public function __construct(public DemandeFonds $demande)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }
    /* public function toMail($notifiable)
    {
        // Déterminer le message en fonction du statut de la demande
        $statusMessage = $this->demande->status == 'approuve' ? 'approuvée' : 'rejetée';

        // Lien vers la situation de la demande de fonds
        $url = route('demandes-fonds.situation');

        return (new MailMessage)
            ->subject('Mise à jour du statut de votre demande de fonds')
            ->greeting('Bonjour ' . $notifiable->name)
            ->line("Votre demande de fonds pour le mois de {$this->demande->mois} a été {$statusMessage}.")
            ->line("Montant de la demande : {$this->demande->montant}")
            ->line("Observation : {$this->demande->observation}")
            ->line('Vous pouvez consulter les détails de votre demande en suivant ce lien :')
            ->action('Voir la demande', $url) // Lien vers la page de situation de la demande
            ->from('bdokeita100@gmail.com', 'Système de gestion des fonds');
    } */
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
            /* 'url' => url("/demandes-fonds/{$this->demande->id}")   */
            'url' => route('demandes-fonds.situation')
        ];
    }
}
