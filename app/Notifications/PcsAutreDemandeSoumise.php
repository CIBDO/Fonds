<?php

namespace App\Notifications;

use App\Models\AutreDemande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PcsAutreDemandeSoumise extends Notification
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
        $poste = $this->demande->poste->nom;
        $montant = number_format($this->demande->montant, 0, ',', ' ');

        return [
            'demande_id' => $this->demande->id,
            'title' => 'Nouvelle Demande Financière Soumise',
            'message' => "Demande de {$poste} pour '{$this->demande->designation}' (Montant: {$montant} FCFA) en attente de validation",
            'url' => route('pcs.autres-demandes.show', $this->demande->id),
            'type' => 'pcs_autre_demande',
            'icon' => 'fas fa-folder-open',
            'color' => 'info'
        ];
    }
}
