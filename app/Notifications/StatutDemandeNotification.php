<?php
use App\Models\DemandeFonds;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatutDemandeNotification extends Notification
{
    use Queueable;

    protected $demande;

    public function __construct(DemandeFonds $demande)
    {
        $this->demande = $demande;
    }

    public function via($notifiable)
    {
        return ['mail']; // ou 'database' si vous utilisez les notifications en base de données
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Mise à jour de votre demande de fonds')
                    ->line('Le statut de votre demande a été mis à jour à : ' . $this->demande->status)
                    ->action('Voir la demande', route('demandes-fonds.show', $this->demande->id))
                    ->line('Merci d\'avoir utilisé notre application !');
    }
}
