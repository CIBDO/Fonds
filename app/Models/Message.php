<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['sujet', 'contenu', 'sender_id'];

    // Relation avec l'expéditeur (l'utilisateur qui a envoyé le message)
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Relation avec les destinataires du message
    public function recipients()
    {
        return $this->belongsToMany(User::class, 'message_users', 'message_id', 'user_id')
                    ->withPivot('lu', 'type')->withTimestamps();
    }

    // Relation avec les pièces jointes
    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
