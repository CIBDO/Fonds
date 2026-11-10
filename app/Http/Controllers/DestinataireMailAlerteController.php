<?php

namespace App\Http\Controllers;

use App\Models\DestinataireMailAlerte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class DestinataireMailAlerteController extends Controller
{
    public function index()
    {
        $destinataires = DestinataireMailAlerte::orderBy('email')->get();
        $evenements = DestinataireMailAlerte::EVENEMENTS;

        return view('alertes-mail.index', compact('destinataires', 'evenements'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        DestinataireMailAlerte::create($data);

        return back()->with('success', 'Destinataire ajouté.');
    }

    public function update(Request $request, DestinataireMailAlerte $destinataire)
    {
        $data = $this->validated($request, $destinataire->id);
        $destinataire->update($data);

        return back()->with('success', 'Destinataire mis à jour.');
    }

    public function destroy(DestinataireMailAlerte $destinataire)
    {
        $destinataire->delete();

        return back()->with('success', 'Destinataire supprimé.');
    }

    public function test(DestinataireMailAlerte $destinataire)
    {
        try {
            Notification::route('mail', $destinataire->email)->notify(new class extends BaseNotification
            {
                public function via($notifiable): array
                {
                    return ['mail'];
                }

                public function toMail($notifiable): MailMessage
                {
                    return (new MailMessage)
                        ->subject('Test alerte e-mail — Gestion des fonds')
                        ->greeting('Bonjour')
                        ->line('Ceci est un e-mail de test de la configuration des alertes.')
                        ->line('Si vous recevez ce message, la diligence e-mail fonctionne.');
                }
            });

            return back()->with('success', 'E-mail de test envoyé à '.$destinataire->email);
        } catch (\Throwable $e) {
            return back()->with('error', 'Échec envoi test : '.$e->getMessage());
        }
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $emailRule = 'required|email|max:255|unique:destinataires_mail_alertes,email';
        if ($ignoreId) {
            $emailRule .= ','.$ignoreId;
        }

        $data = $request->validate([
            'email' => $emailRule,
            'nom' => 'nullable|string|max:150',
            'actif' => 'nullable|boolean',
            'fonds_soumise' => 'nullable|boolean',
            'fonds_statut' => 'nullable|boolean',
            'autre_soumise' => 'nullable|boolean',
            'autre_validee' => 'nullable|boolean',
            'autre_rejetee' => 'nullable|boolean',
            'vir_soumission' => 'nullable|boolean',
            'vir_paiement' => 'nullable|boolean',
        ]);

        foreach (array_keys(DestinataireMailAlerte::EVENEMENTS) as $flag) {
            $data[$flag] = $request->boolean($flag);
        }
        $data['actif'] = $request->boolean('actif', true);

        return $data;
    }
}
