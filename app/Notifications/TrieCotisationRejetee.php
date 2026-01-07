<?php

namespace App\Notifications;

use App\Models\CotisationTrie;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TrieCotisationRejetee extends Notification
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
        $motif = $this->cotisation->motif_rejet ?? 'Aucun motif spécifié';

        return [
            'cotisation_id' => $this->cotisation->id,
            'title' => 'Cotisation TRIE Rejetée',
            'message' => "Votre cotisation TRIE de {$poste} - {$bureau} pour {$mois} {$annee} a été rejetée. Motif: {$motif}",
            'url' => route('trie.cotisations.show', $this->cotisation->id),
            'type' => 'trie_cotisation',
            'icon' => 'fas fa-times-circle',
            'color' => 'danger'
        ];
    }
}

