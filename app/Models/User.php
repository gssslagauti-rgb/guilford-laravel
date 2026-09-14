<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['display_name', 'email', 'password', 'role', 'is_banned'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_banned' => 'boolean',
        'last_login' => 'datetime',
    ];

    public function isModerator(): bool
    {
        return in_array($this->role, ['moderator', 'admin'], true);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }
}