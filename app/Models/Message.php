<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['sender_id', 'receiver_id', 'subject', 'body', 'status'];

    // Relation avec l'expéditeur (sender)
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Relation avec le destinataire (receiver)
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function recipients()
{
    return $this->belongsToMany(User::class, 'message_recipients')->withPivot('type', 'lu'); // Assurez-vous que le nom de la table pivot est correct
}
// Message.php
public function attachments()
{
    return $this->hasMany(Attachment::class);
}


}

