<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'status',
        'bot_unmatched_count',
        'staff_id',
        'last_message_at',
    ];

    protected $casts = [
        'bot_unmatched_count' => 'integer',
        'last_message_at' => 'datetime',
    ];

    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting_staff');
    }

    public function scopeStaffActive($query, $staffId = null)
    {
        $q = $query->where('status', 'staff_connected');
        if ($staffId) {
            $q->where('staff_id', $staffId);
        }
        return $q;
    }

    public function scopeBotActive($query)
    {
        return $query->where('status', 'bot');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id')->orderBy('id');
    }

    public function unreadCount()
    {
        return $this->messages()->where('is_read', false)->where('sender_type', 'customer')->count();
    }
}
