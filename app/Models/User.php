<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'must_change_password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    public const ROLE_SUPER_ADMIN = 'SUPER_ADMIN';
    public const ROLE_MANAGER = 'MANAGER';
    public const ROLE_STAFF = 'STAFF';

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER || $this->isSuperAdmin();
    }

    public function isStaff(): bool
    {
        return in_array($this->role, [self::ROLE_STAFF, self::ROLE_MANAGER, self::ROLE_SUPER_ADMIN], true);
    }

    public function canManageUsers(): bool
    {
        return $this->isSuperAdmin();
    }
}
