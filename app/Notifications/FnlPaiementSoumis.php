<?php

namespace App\Notifications;

use App\Models\PaiementFnl;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FnlPaiementSoumis extends Notification
{
    use Queueable;

    public function __construct(protected PaiementFnl $paiement)
    {
        $this->paiement->loadMissing('poste');
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $poste = $this->paiement->poste?->libelleAffiche() ?? 'Poste inconnu';
        $mois = $this->paiement->nom_mois;
        $annee = $this->paiement->annee;
        $retenue = number_format($this->paiement->retenue_fnl ?? 0, 0, ',', ' ');

        return (new MailMessage)
            ->subject("Paiement FNL soumis — {$poste}")
            ->greeting('Bonjour')
            ->line("Un paiement FNL a été soumis et attend validation.")
            ->line("Poste / RP : {$poste}")
            ->line("Période : {$mois} {$annee}")
            ->line("Retenue FNL : {$retenue} FCFA")
            ->action('Voir le paiement', route('fnl.paiements.show', $this->paiement->id))
            ->line('Merci de procéder à la validation.');
    }

    public function toArray($notifiable)
    {
        $poste = $this->paiement->poste?->libelleAffiche() ?? 'Poste inconnu';
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
