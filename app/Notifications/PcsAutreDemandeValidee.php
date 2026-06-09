<?php

namespace App\Notifications;

use App\Models\AutreDemande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PcsAutreDemandeValidee extends Notification
{
    use Queueable;

    protected $demande;

    public function __construct(AutreDemande $demande)
    {
        $this->demande = $demande;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $montantAccorde = number_format($this->demande->montant_accord, 0, ',', ' ');
        $montantDemande = number_format($this->demande->montant, 0, ',', ' ');

        $montantVerse = number_format($this->demande->montant_verse, 0, ',', ' ');

        if ($this->demande->statut === 'valide') {
            $message = "Votre demande '{$this->demande->designation}' est entièrement validée ({$montantAccorde} FCFA versés)";
        } elseif ($this->demande->montant_verse > 0) {
            $reste = number_format($this->demande->montant_restant_accord, 0, ',', ' ');
            $message = "Versement de {$montantVerse} FCFA enregistré pour '{$this->demande->designation}'. Reste à verser : {$reste} FCFA";
        } elseif ($this->demande->montant_accord == $this->demande->montant) {
            $message = "Votre demande '{$this->demande->designation}' a été validée avec le montant complet ({$montantAccorde} FCFA)";
        } else {
            $message = "Votre demande '{$this->demande->designation}' a été validée avec un montant de {$montantAccorde} FCFA (demandé: {$montantDemande} FCFA)";
        }

        return [
            'demande_id' => $this->demande->id,
            'title' => 'Demande Financière Validée',
            'message' => $message,
            'url' => route('pcs.autres-demandes.show', $this->demande->id),
            'type' => 'pcs_autre_demande',
            'icon' => 'fas fa-check-circle',
            'color' => 'success'
        ];
    }
}
