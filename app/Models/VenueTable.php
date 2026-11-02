<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenueTable extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code',
        'zone',
        'capacity',
        'seats',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'tableCode', 'code');
    }
}
