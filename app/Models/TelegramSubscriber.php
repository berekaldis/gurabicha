<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'first_name',
        'last_name',
        'username',
        'favorite_club',
        'interest',
        'is_active',
        'last_interaction_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_interaction_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForAudience(Builder $query, ?string $audience): Builder
    {
        if (empty($audience) || $audience === 'ALL') {
            return $query->where('is_active', true);
        }

        if ($audience === 'ACOUSTIC') {
            return $query->where('is_active', true)
                ->where(function ($q) {
                    $q->where('interest', 'ACOUSTIC')
                      ->orWhere('interest', 'ALL');
                });
        }

        // Audience is a club (e.g., 'Arsenal', 'Chelsea', 'Manchester United', 'Liverpool')
        return $query->where('is_active', true)
            ->where(function ($q) use ($audience) {
                $q->where('favorite_club', $audience)
                  ->orWhereNull('favorite_club'); // Also include general fans
            });
    }

    public function getDisplayNameAttribute(): string
    {
        if (!empty($this->first_name)) {
            return trim("{$this->first_name} {$this->last_name}");
        }

        if (!empty($this->username)) {
            return "@{$this->username}";
        }

        return "Chat #{$this->chat_id}";
    }
}
