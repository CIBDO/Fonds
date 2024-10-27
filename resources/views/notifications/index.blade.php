@if($notification->type === 'App\Notifications\MessageSent')
    <span class="text-success">Nouveau message:</span>
    {{ $notification->data['subject'] ?? 'Message non disponible' }}
    <small>de {{ $notification->data['sender_name'] ?? 'Expéditeur inconnu' }}</small>
@endif
