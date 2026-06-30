<?php

declare(strict_types=1);

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Wallet extends Model
{
    use HasFactory;

    // SECURITY: Only these fields can be mass-assigned
    protected $fillable = [
        'name',
        'type',
        'currency',
        'color',
        'icon',
        'is_active',
    ];

    // SECURITY: balance is NOT fillable - only modified via increment()/decrement()
    protected $guarded = [
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'from_wallet_id');
    }

    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'to_wallet_id');
    }

    public function savingsGoals(): HasMany
    {
        return $this->hasMany(SavingsGoal::class);
    }

    public function getFormattedBalanceAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->balance, 0, ',', '.');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
