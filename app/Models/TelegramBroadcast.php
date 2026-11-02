<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramBroadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'target_audience',
        'message',
        'action_button_text',
        'action_button_url',
        'recipients_count',
        'status',
        'sent_by_name',
    ];

    protected $casts = [
        'recipients_count' => 'integer',
        'created_at' => 'datetime',
    ];
}
