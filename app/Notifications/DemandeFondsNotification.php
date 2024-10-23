<?php

namespace App\Notifications;

use App\Models\DemandeFonds;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DemandeFondsNotification extends Notification
{
    use Queueable;

    protected $demandeFonds;

    public function __construct(DemandeFonds $demandeFonds)
    {
        $this->demandeFonds = $demandeFonds;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'demande_id' => $this->demandeFonds->id,
            'poste' => $this->demandeFonds->poste->nom,
            'mois' => $this->demandeFonds->mois,
            'montant' => $this->demandeFonds->total_courant,
            'statut' => $this->demandeFonds->status,
            'message' => "Nouvelle demande de fonds créée pour {$this->demandeFonds->poste->nom}",
            'type' => 'demande_fonds'
        ];
    }
}
