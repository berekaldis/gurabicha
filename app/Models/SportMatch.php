<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SportMatch extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'matches';

    protected $fillable = [
        'homeTeam',
        'awayTeam',
        'competition',
        'season',
        'stage',
        'venue',
        'kickoff',
        'status',
        'homeScore',
        'awayScore',
        'featured',
        'live',
    ];

    protected $casts = [
        'kickoff' => 'datetime',
        'homeScore' => 'integer',
        'awayScore' => 'integer',
        'featured' => 'boolean',
        'live' => 'boolean',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'matchId', 'id');
    }
}
