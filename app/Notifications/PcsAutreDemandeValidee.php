<?php

namespace App\Notifications;

use App\Models\AutreDemande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PcsAutreDemandeValidee extends Notification
{
    use Queueable;

    protected $demande;
    protected $versementSupplementaire;

    public function __construct(AutreDemande $demande, bool $versementSupplementaire = false)
    {
        $this->demande = $demande;
        $this->versementSupplementaire = $versementSupplementaire;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Demande financière validée')
            ->greeting('Bonjour '.($notifiable->name ?? ''))
            ->line($this->buildMessage())
            ->action('Voir la demande', route('pcs.autres-demandes.show', $this->demande->id));
    }

    public function toArray($notifiable)
    {
        return [
            'demande_id' => $this->demande->id,
            'title' => 'Demande Financière Validée',
            'message' => $this->buildMessage(),
            'url' => route('pcs.autres-demandes.show', $this->demande->id),
            'type' => 'pcs_autre_demande',
            'icon' => 'fas fa-check-circle',
            'color' => 'success',
        ];
    }

    private function buildMessage(): string
    {
        $montantAccorde = number_format($this->demande->montant_accord, 0, ',', ' ');
        $montantDemande = number_format($this->demande->montant, 0, ',', ' ');
        $montantVerse = number_format($this->demande->montant_verse, 0, ',', ' ');

        if ($this->versementSupplementaire) {
            return "Versement supplémentaire enregistré pour '{$this->demande->designation}'. Total versé : {$montantVerse} FCFA";
        }
        if ($this->demande->statut === 'valide') {
            return "Votre demande '{$this->demande->designation}' est entièrement validée ({$montantAccorde} FCFA versés)";
        }
        if ($this->demande->montant_verse > 0) {
            $reste = number_format($this->demande->montant_restant_accord, 0, ',', ' ');

            return "Versement de {$montantVerse} FCFA enregistré pour '{$this->demande->designation}'. Reste à verser : {$reste} FCFA";
        }
        if ($this->demande->montant_accord == $this->demande->montant) {
            return "Votre demande '{$this->demande->designation}' a été validée avec le montant complet ({$montantAccorde} FCFA)";
        }

        return "Votre demande '{$this->demande->designation}' a été validée avec un montant de {$montantAccorde} FCFA (demandé: {$montantDemande} FCFA)";
    }
}
