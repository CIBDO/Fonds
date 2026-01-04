<?php

namespace App\Notifications;

use App\Models\DeclarationPcs;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PcsDeclarationValidee extends Notification
{
    use Queueable;

    protected $declaration;

    public function __construct(DeclarationPcs $declaration)
    {
        $this->declaration = $declaration;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $nomEntite = $this->declaration->nom_entite;
        $programme = $this->declaration->programme;
        $mois = \Carbon\Carbon::create()->month((int)$this->declaration->mois)->translatedFormat('F');
        $annee = $this->declaration->annee;

        return [
            'declaration_id' => $this->declaration->id,
            'title' => 'Déclaration PCS Validée',
            'message' => "Votre déclaration PCS {$programme} de {$nomEntite} pour {$mois} {$annee} a été validée",
            'url' => route('pcs.declarations.show', $this->declaration->id),
            'type' => 'pcs_declaration',
            'icon' => 'fas fa-check-circle',
            'color' => 'success'
        ];
    }
}
