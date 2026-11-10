<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class VirementPaye extends Notification
{
    use Queueable;

    /** @param  Collection<int, \App\Models\Virement>  $virements */
    public function __construct(public Collection $virements)
    {
        $this->virements->loadMissing(['poste', 'banque']);
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $total = number_format($this->virements->sum('montant'), 0, ',', ' ');
        $mail = (new MailMessage)
            ->subject('Virements salaires marqués payés')
            ->greeting('Bonjour')
            ->line($this->virements->count().' virement(s) ont été marqués comme payés (exécution bancaire).')
            ->line("Montant total : {$total} FCFA");

        foreach ($this->virements->take(15) as $v) {
            $mail->line(sprintf(
                '• %s | %s | %s | %s %s | %s FCFA',
                $v->numero_vir,
                $v->poste->nom ?? '-',
                $v->banque->nom ?? '-',
                $v->mois,
                $v->annee,
                number_format((float) $v->montant, 0, ',', ' ')
            ));
        }

        if ($this->virements->count() > 15) {
            $mail->line('… et '.($this->virements->count() - 15).' autre(s).');
        }

        return $mail->action('Voir la consolidation', route('virements.acct.consolidation'));
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Virements payés',
            'message' => $this->virements->count().' VIR marqués payés',
            'url' => route('virements.acct.consolidation'),
            'type' => 'virement_paye',
        ];
    }
}
