<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'uuid'];

    protected $hidden = ['password', 'remember_token'];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->is_admin ?? false;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function paidOrders(): HasMany
    {
        return $this->hasMany(Order::class)->where('status', Order::STATUS_PAID);
    }

    public function cart(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get slug-friendly username from name
     */
    public function getUsernameAttribute(): string
    {
        return Str::slug($this->name, '');
    }

    /**
     * Get the user's dashboard URL
     */
    public function getDashboardUrlAttribute(): string
    {
        return route('user.dashboard.show', [
            'username' => $this->username,
            'uuid' => $this->uuid,
        ]);
    }
}
