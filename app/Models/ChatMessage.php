<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';

    protected $fillable = [
        'chat_conversation_id',
        'role',
        'message',
    ];

    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'chat_conversation_id');
    }
}