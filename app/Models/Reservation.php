<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code',
        'date',
        'slot',
        'tableCode',
        'guests',
        'name',
        'phone',
        'email',
        'request',
        'matchId',
        'matchLabel',
        'telegramUserId',
        'telegramUsername',
        'status',
    ];

    protected $casts = [
        'date' => 'datetime',
        'guests' => 'integer',
    ];

    public function table()
    {
        return $this->belongsTo(VenueTable::class, 'tableCode', 'code');
    }

    public function match()
    {
        return $this->belongsTo(SportMatch::class, 'matchId', 'id');
    }
}
