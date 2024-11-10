<?php

namespace App\Notifications;

use App\Models\DemandeFonds;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
class DemandeFondsNotification extends Notification
{
    use Queueable;

    protected $demandeFonds;

    public function __construct(DemandeFonds $demandeFonds)
    {
        $this->demandeFonds = $demandeFonds;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $url = route('login');  // URL de la page de connexion
        return (new MailMessage)
            ->subject('Nouvelle demande de fonds')
            ->greeting('Bonjour ' . $notifiable->name)
            ->line("Une nouvelle demande de fonds a été créée par {$this->demandeFonds->user->name} pour le poste {$this->demandeFonds->poste->nom}.")
            ->line("Le montant demandé pour ce mois est de : {$this->demandeFonds->total_courant}")
            ->line("Le statut de la demande est : {$this->demandeFonds->status}")
            ->action('Voir la demande', $url)
            ->line('Merci de consulter la demande de fonds pour plus de détails.')
            ->from('bdokeita100@gmail.com', 'Système de gestion des fonds'); // Définir l'expéditeur
    }

    public function toArray($notifiable)
    {
        return [
            'demande_id' => $this->demandeFonds->id,
            'poste' => $this->demandeFonds->poste->nom,
            'mois' => $this->demandeFonds->mois,
            'montant' => $this->demandeFonds->total_courant,
            'statut' => $this->demandeFonds->status,
            'message' => "Nouvelle demande de fonds créée par {$this->demandeFonds->user->name} pour {$this->demandeFonds->poste->nom}",
            'type' => 'demande_fonds',
            /* 'url' => url("/demandes-fonds/{$this->demandeFonds->id}")  */
            'url' => route('demandes-fonds.situationDF')
        ];
    }

}
