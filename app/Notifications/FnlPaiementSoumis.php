<?php

namespace App\Notifications;

use App\Models\PaiementFnl;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FnlPaiementSoumis extends Notification
{
    use Queueable;

    public function __construct(protected PaiementFnl $paiement)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        if (!$this->paiement->relationLoaded('poste')) {
            $this->paiement->load('poste');
        }

        $poste = $this->paiement->poste ? $this->paiement->poste->nom : 'Poste inconnu';
        $mois = $this->paiement->nom_mois;
        $annee = $this->paiement->annee;
        $retenue = number_format($this->paiement->retenue_fnl ?? 0, 0, ',', ' ');

        return [
            'paiement_id' => $this->paiement->id,
            'title' => 'Paiement FNL à valider',
            'message' => "Paiement FNL de {$poste} pour {$mois} {$annee} (Retenue FNL : {$retenue} FCFA) en attente de validation",
            'url' => route('fnl.paiements.show', $this->paiement->id),
            'type' => 'fnl_paiement',
            'icon' => 'fas fa-home',
            'color' => 'info',
        ];
    }
}
