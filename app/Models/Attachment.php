<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = ['message_id', 'filename', 'filepath'];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
    public function getPublicUrlAttribute()
    {
        return Storage::url($this->filepath);
    }
}
