<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function isSuperAdmin(): bool
    {
        return strtolower($this->role) === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return in_array(strtolower($this->role), ['superadmin', 'admin']);
    }

    public function isModerator(): bool
    {
        return str_contains(strtolower($this->role), 'moder');
    }

    public function isStaff(): bool
    {
        return $this->isAdmin() || $this->isModerator();
    }
}
