<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'description',
        'category',
        'date',
        'startTime',
        'endTime',
        'imageUrl',
        'featured',
    ];

    protected $casts = [
        'date' => 'datetime',
        'featured' => 'boolean',
    ];
}
