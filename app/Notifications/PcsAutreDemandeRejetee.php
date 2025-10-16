<?php

namespace App\Notifications;

use App\Models\AutreDemande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class PcsAutreDemandeRejetee extends Notification
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
        return [
            'demande_id' => $this->demande->id,
            'title' => 'Demande Financière Rejetée',
            'message' => "Votre demande '{$this->demande->designation}' a été rejetée. Motif: " . Str::limit($this->demande->motif_rejet, 100),
            'url' => route('pcs.autres-demandes.show', $this->demande->id),
            'type' => 'pcs_autre_demande',
            'icon' => 'fas fa-times-circle',
            'color' => 'danger'
        ];
    }
}
