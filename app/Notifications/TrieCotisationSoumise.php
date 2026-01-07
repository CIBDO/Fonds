<?php

namespace App\Notifications;

use App\Models\CotisationTrie;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TrieCotisationSoumise extends Notification
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
        // Charger les relations si elles ne sont pas déjà chargées
        if (!$this->cotisation->relationLoaded('poste')) {
            $this->cotisation->load('poste');
        }
        if (!$this->cotisation->relationLoaded('bureauTrie')) {
            $this->cotisation->load('bureauTrie');
        }

        $poste = $this->cotisation->poste ? $this->cotisation->poste->nom : 'Poste inconnu';
        $bureau = $this->cotisation->bureauTrie ? $this->cotisation->bureauTrie->nom_bureau : 'Bureau inconnu';
        $mois = \Carbon\Carbon::create()->month((int)$this->cotisation->mois)->translatedFormat('F');
        $annee = $this->cotisation->annee;
        $montant = number_format($this->cotisation->montant_total ?? 0, 0, ',', ' ');

        return [
            'cotisation_id' => $this->cotisation->id,
            'title' => 'Nouvelle Cotisation TRIE Soumise',
            'message' => "Cotisation TRIE de {$poste} - {$bureau} pour {$mois} {$annee} (Montant: {$montant} FCFA) en attente de validation",
            'url' => route('trie.cotisations.show', $this->cotisation->id),
            'type' => 'trie_cotisation',
            'icon' => 'fas fa-coins',
            'color' => 'info'
        ];
    }
}

