<?php

namespace App\Services;

use App\Models\DestinataireMailAlerte;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class MailAlerteService
{
    public static function notifyEvenement(string $evenement, Notification $notification): void
    {
        $emails = DestinataireMailAlerte::pourEvenement($evenement)
            ->pluck('email')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($emails === []) {
            return;
        }

        try {
            NotificationFacade::route('mail', $emails)->notify($notification);
        } catch (\Throwable $e) {
            Log::warning('Échec envoi alerte mail '.$evenement.': '.$e->getMessage());
        }
    }
}
