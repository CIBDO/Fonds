<?php

namespace App\Notifications;

use App\Models\CotisationTrie;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TrieCotisationValidee extends Notification
{
    use Queueable;

    protected $cotisation;

    public function __construct(CotisationTrie $cotisation)
    {
        $this->cotisation = $cotisation;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $poste = $this->cotisation->poste->nom;
        $bureau = $this->cotisation->bureauTrie->nom_bureau;
        $mois = \Carbon\Carbon::create()->month((int)$this->cotisation->mois)->translatedFormat('F');
        $annee = $this->cotisation->annee;
        $montant = number_format($this->cotisation->montant_total, 0, ',', ' ');

        return [
            'cotisation_id' => $this->cotisation->id,
            'title' => 'Cotisation TRIE Validée',
            'message' => "Votre cotisation TRIE de {$poste} - {$bureau} pour {$mois} {$annee} (Montant: {$montant} FCFA) a été validée",
            'url' => route('trie.cotisations.show', $this->cotisation->id),
            'type' => 'trie_cotisation',
            'icon' => 'fas fa-check-circle',
            'color' => 'success'
        ];
    }
}

