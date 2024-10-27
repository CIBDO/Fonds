@if($notification->type === 'App\Notifications\MessageSent')
    <span class="text-success">Nouveau message:</span>
    <p>Vous avez un message de la part de {{ $notification->data['sender_name'] ?? 'Expéditeur inconnu' }}.</p>
    <p>Sujet: {{ $notification->data['subject'] ?? 'Sujet non disponible' }}</p>
@endif
