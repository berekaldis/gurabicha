<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'favorite_club',
        'notify_matchday',
        'notify_scores',
        'notify_acoustic',
        'notes',
    ];

    protected $casts = [
        'notify_matchday' => 'boolean',
        'notify_scores' => 'boolean',
        'notify_acoustic' => 'boolean',
    ];
}
