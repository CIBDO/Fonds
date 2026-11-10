<?php

namespace App\Notifications;

use App\Models\BordereauVirement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BordereauVirementSoumis extends Notification
{
    use Queueable;

    public function __construct(public BordereauVirement $bordereau)
    {
        $this->bordereau->loadMissing(['poste', 'virements']);
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $poste = $this->bordereau->poste->nom ?? 'Poste';
        $total = number_format($this->bordereau->virements->sum('montant'), 0, ',', ' ');
        $nb = $this->bordereau->virements->count();

        return (new MailMessage)
            ->subject("Bordereau de virements soumis — {$poste}")
            ->greeting('Bonjour')
            ->line("Un bordereau de propositions de virements a été soumis par {$poste}.")
            ->line("Période : {$this->bordereau->mois} {$this->bordereau->annee}")
            ->line("Nombre de VIR : {$nb}")
            ->line("Montant total : {$total} FCFA")
            ->action('Voir les virements', route('virements.acct.index'))
            ->line('Merci de procéder au traitement.');
    }

    public function toArray($notifiable): array
    {
        $poste = $this->bordereau->poste->nom ?? 'Poste';

        return [
            'bordereau_id' => $this->bordereau->id,
            'title' => 'Bordereau de virements soumis',
            'message' => "Bordereau VIR de {$poste} ({$this->bordereau->mois} {$this->bordereau->annee}) en attente de traitement",
            'url' => route('virements.acct.index'),
            'type' => 'bordereau_virement',
            'icon' => 'fas fa-university',
            'color' => 'info',
        ];
    }
}
