<?php

namespace App\Notifications;

use App\Models\DemandeFonds;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemandeFondsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $demandeFonds;

    public function __construct(DemandeFonds $demandeFonds)
    {
        $this->demandeFonds = $demandeFonds;
    }

    public function via($notifiable)
    {
        return ['database']; // Vous pouvez aussi envoyer par mail si nécessaire
    }

    public function toArray($notifiable)
    {
        return [
            'demande_id' => $this->demandeFonds->id,
            'poste' => $this->demandeFonds->poste->nom,
            'mois' => $this->demandeFonds->mois,
            'montant' => $this->demandeFonds->total_courant,
            'statut' => $this->demandeFonds->status,
        ];
    }
}
