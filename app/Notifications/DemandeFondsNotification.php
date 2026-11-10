<?php

namespace App\Notifications;

use App\Models\DemandeFonds;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemandeFondsNotification extends Notification
{
    use Queueable;

    protected $demandeFonds;

    public function __construct(DemandeFonds $demandeFonds)
    {
        $this->demandeFonds = $demandeFonds;
        $this->demandeFonds->loadMissing(['user', 'poste']);
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $poste = $this->demandeFonds->poste->nom ?? 'Poste';
        $montant = number_format((float) ($this->demandeFonds->total_courant ?? 0), 0, ',', ' ');

        return (new MailMessage)
            ->subject('Nouvelle demande de fonds')
            ->greeting('Bonjour '.($notifiable->name ?? ''))
            ->line("Une nouvelle demande de fonds a été créée par {$this->demandeFonds->user->name} pour le poste {$poste}.")
            ->line("Mois : {$this->demandeFonds->mois} {$this->demandeFonds->annee}")
            ->line("Montant demandé : {$montant} FCFA")
            ->action('Voir les demandes', route('demandes-fonds.envois'))
            ->line('Merci de consulter la demande de fonds pour plus de détails.');
    }

    public function toArray($notifiable)
    {
        return [
            'demande_id' => $this->demandeFonds->id,
            'poste' => $this->demandeFonds->poste->nom ?? null,
            'mois' => $this->demandeFonds->mois,
            'montant' => $this->demandeFonds->total_courant,
            'statut' => $this->demandeFonds->status,
            'message' => "Nouvelle demande de fonds créée par {$this->demandeFonds->user->name} pour {$this->demandeFonds->poste->nom}",
            'type' => 'demande_fonds',
            'url' => route('demandes-fonds.situationDF'),
        ];
    }
}
