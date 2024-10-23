<?php

namespace App\Notifications;

use App\Models\DemandeFonds;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

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

    public function toArray($notifiable)
    {
        $statusMessage = $this->demande->status == 'approuve' ? 'approuvée' : 'rejetée';
        
        return [
            'demande_id' => $this->demande->id,
            'status' => $this->demande->status,
            'message' => "Votre demande de fonds pour le mois de {$this->demande->mois} a été {$statusMessage}",
            'montant' => $this->demande->montant,
            'observation' => $this->demande->observation,
            'type' => 'status_update'
        ];
    }
}
